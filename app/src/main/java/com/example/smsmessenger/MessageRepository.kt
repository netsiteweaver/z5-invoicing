package com.example.smsmessenger

import android.content.Context
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class MessageRepository(private val context: Context) {
    
    private val apiService = NetworkModule.apiService
    private val smsManager = SmsManager(context)
    
    suspend fun fetchAndSendMessages(): List<Message> = withContext(Dispatchers.IO) {
        try {
            // Fetch messages from API
            val response = apiService.getMessages()
            
            if (response.isSuccessful) {
                val messages = response.body() ?: emptyList()
                val processedMessages = mutableListOf<Message>()
                
                // Process each unsent message
                for (message in messages.filter { !it.sent }) {
                    if (smsManager.hasSmsPermission()) {
                        val sent = smsManager.sendSms(message.recipient, message.content)
                        
                        if (sent) {
                            // Mark message as sent on server
                            try {
                                apiService.markAsSent(message.id, message.copy(sent = true))
                            } catch (e: Exception) {
                                e.printStackTrace()
                            }
                            
                            processedMessages.add(message.copy(sent = true))
                        } else {
                            processedMessages.add(message)
                        }
                    } else {
                        processedMessages.add(message)
                    }
                }
                
                processedMessages
            } else {
                emptyList()
            }
        } catch (e: Exception) {
            e.printStackTrace()
            emptyList()
        }
    }
    
    suspend fun fetchMessages(): List<Message> = withContext(Dispatchers.IO) {
        try {
            val response = apiService.getMessages()
            if (response.isSuccessful) {
                response.body() ?: emptyList()
            } else {
                emptyList()
            }
        } catch (e: Exception) {
            e.printStackTrace()
            emptyList()
        }
    }
}