package com.example.smsmessenger

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.example.smsmessenger.databinding.ItemMessageBinding
import java.text.SimpleDateFormat
import java.util.*

class MessageAdapter : RecyclerView.Adapter<MessageAdapter.MessageViewHolder>() {
    
    private var messages = mutableListOf<Message>()
    private val dateFormat = SimpleDateFormat("MMM dd, yyyy HH:mm", Locale.getDefault())
    
    fun updateMessages(newMessages: List<Message>) {
        messages.clear()
        messages.addAll(newMessages)
        notifyDataSetChanged()
    }
    
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MessageViewHolder {
        val binding = ItemMessageBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return MessageViewHolder(binding)
    }
    
    override fun onBindViewHolder(holder: MessageViewHolder, position: Int) {
        holder.bind(messages[position])
    }
    
    override fun getItemCount(): Int = messages.size
    
    class MessageViewHolder(private val binding: ItemMessageBinding) : 
        RecyclerView.ViewHolder(binding.root) {
        
        private val dateFormat = SimpleDateFormat("MMM dd, yyyy HH:mm", Locale.getDefault())
        
        fun bind(message: Message) {
            binding.apply {
                textMessageContent.text = message.content
                textRecipient.text = "To: ${message.recipient}"
                textTimestamp.text = dateFormat.format(Date(message.timestamp))
                
                // Show status
                if (message.sent) {
                    textStatus.text = "✓ Sent"
                    textStatus.setTextColor(android.graphics.Color.GREEN)
                } else {
                    textStatus.text = "Pending"
                    textStatus.setTextColor(android.graphics.Color.ORANGE)
                }
            }
        }
    }
}