package com.example.smsmessenger

import android.app.Notification
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.app.Service
import android.content.Context
import android.content.Intent
import android.os.Build
import android.os.IBinder
import androidx.core.app.NotificationCompat
import androidx.work.*
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import java.util.concurrent.TimeUnit

class MessageSyncService : Service() {
    
    private val serviceScope = CoroutineScope(Dispatchers.IO + Job())
    private lateinit var messageRepository: MessageRepository
    
    companion object {
        const val CHANNEL_ID = "MessageSyncChannel"
        const val NOTIFICATION_ID = 1
        
        fun startService(context: Context) {
            val intent = Intent(context, MessageSyncService::class.java)
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                context.startForegroundService(intent)
            } else {
                context.startService(intent)
            }
        }
    }
    
    override fun onCreate() {
        super.onCreate()
        messageRepository = MessageRepository(this)
        createNotificationChannel()
    }
    
    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        startForeground(NOTIFICATION_ID, createNotification())
        startPeriodicSync()
        return START_STICKY
    }
    
    override fun onBind(intent: Intent?): IBinder? = null
    
    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                CHANNEL_ID,
                "Message Sync Service",
                NotificationManager.IMPORTANCE_LOW
            ).apply {
                description = "Background service for syncing and sending SMS messages"
            }
            
            val notificationManager = getSystemService(NotificationManager::class.java)
            notificationManager.createNotificationChannel(channel)
        }
    }
    
    private fun createNotification(): Notification {
        val notificationIntent = Intent(this, MainActivity::class.java)
        val pendingIntent = PendingIntent.getActivity(
            this, 0, notificationIntent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )
        
        return NotificationCompat.Builder(this, CHANNEL_ID)
            .setContentTitle("SMS Messenger")
            .setContentText("Syncing messages...")
            .setSmallIcon(android.R.drawable.ic_dialog_email)
            .setContentIntent(pendingIntent)
            .build()
    }
    
    private fun startPeriodicSync() {
        serviceScope.launch {
            while (true) {
                try {
                    val messages = messageRepository.fetchAndSendMessages()
                    updateNotification("Processed ${messages.size} messages")
                } catch (e: Exception) {
                    updateNotification("Sync failed: ${e.message}")
                }
                
                // Wait 5 minutes before next sync
                delay(5 * 60 * 1000)
            }
        }
    }
    
    private fun updateNotification(contentText: String) {
        val notification = NotificationCompat.Builder(this, CHANNEL_ID)
            .setContentTitle("SMS Messenger")
            .setContentText(contentText)
            .setSmallIcon(android.R.drawable.ic_dialog_email)
            .build()
        
        val notificationManager = getSystemService(NotificationManager::class.java)
        notificationManager.notify(NOTIFICATION_ID, notification)
    }
}

class MessageSyncWorker(
    context: Context,
    params: WorkerParameters
) : Worker(context, params) {
    
    private val messageRepository = MessageRepository(context)
    
    override fun doWork(): Result {
        return try {
            val messages = messageRepository.fetchAndSendMessages()
            Result.success()
        } catch (e: Exception) {
            Result.failure()
        }
    }
    
    companion object {
        fun enqueuePeriodicWork(context: Context) {
            val constraints = Constraints.Builder()
                .setRequiredNetworkType(NetworkType.CONNECTED)
                .build()
            
            val workRequest = PeriodicWorkRequestBuilder<MessageSyncWorker>(
                15, TimeUnit.MINUTES
            ).setConstraints(constraints)
                .build()
            
            WorkManager.getInstance(context).enqueueUniquePeriodicWork(
                "message_sync_work",
                ExistingPeriodicWorkPolicy.KEEP,
                workRequest
            )
        }
    }
}