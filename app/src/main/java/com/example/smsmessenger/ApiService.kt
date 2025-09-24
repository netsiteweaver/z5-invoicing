package com.example.smsmessenger

import retrofit2.Response
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path
import retrofit2.http.Body

interface ApiService {
    @GET("messages")
    suspend fun getMessages(): Response<List<Message>>
    
    @GET("messages/{id}")
    suspend fun getMessage(@Path("id") id: String): Response<Message>
    
    @POST("messages/{id}/sent")
    suspend fun markAsSent(@Path("id") id: String, @Body message: Message): Response<Unit>
}