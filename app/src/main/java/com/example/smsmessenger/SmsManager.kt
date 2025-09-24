package com.example.smsmessenger

import android.Manifest
import android.content.Context
import android.content.pm.PackageManager
import android.telephony.SmsManager
import androidx.core.app.ActivityCompat
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class SmsManager(private val context: Context) {
    
    private val smsManager = SmsManager.getDefault()
    
    suspend fun sendSms(phoneNumber: String, message: String): Boolean = withContext(Dispatchers.IO) {
        try {
            if (ActivityCompat.checkSelfPermission(
                    context,
                    Manifest.permission.SEND_SMS
                ) != PackageManager.PERMISSION_GRANTED
            ) {
                return@withContext false
            }
            
            smsManager.sendTextMessage(phoneNumber, null, message, null, null)
            true
        } catch (e: Exception) {
            e.printStackTrace()
            false
        }
    }
    
    fun hasSmsPermission(): Boolean {
        return ActivityCompat.checkSelfPermission(
            context,
            Manifest.permission.SEND_SMS
        ) == PackageManager.PERMISSION_GRANTED
    }
}