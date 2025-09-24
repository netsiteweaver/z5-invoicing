package com.example.smsmessenger

import com.google.gson.annotations.SerializedName

data class Message(
    @SerializedName("id")
    val id: String,
    
    @SerializedName("content")
    val content: String,
    
    @SerializedName("recipient")
    val recipient: String,
    
    @SerializedName("timestamp")
    val timestamp: Long,
    
    @SerializedName("sent")
    val sent: Boolean = false
)