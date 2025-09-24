package com.example.smsmessenger

import android.Manifest
import android.content.pm.PackageManager
import android.os.Bundle
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.example.smsmessenger.databinding.ActivityMainBinding
import kotlinx.coroutines.launch

class MainActivity : AppCompatActivity() {
    
    private lateinit var binding: ActivityMainBinding
    private lateinit var messageRepository: MessageRepository
    private lateinit var messageAdapter: MessageAdapter
    
    private val requestPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { isGranted: Boolean ->
        if (isGranted) {
            Toast.makeText(this, "SMS permission granted", Toast.LENGTH_SHORT).show()
            fetchAndSendMessages()
        } else {
            Toast.makeText(this, "SMS permission denied", Toast.LENGTH_SHORT).show()
        }
    }
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)
        
        messageRepository = MessageRepository(this)
        setupRecyclerView()
        setupClickListeners()
        
        checkPermissions()
    }
    
    private fun setupRecyclerView() {
        messageAdapter = MessageAdapter()
        binding.recyclerViewMessages.apply {
            layoutManager = LinearLayoutManager(this@MainActivity)
            adapter = messageAdapter
        }
    }
    
    private fun setupClickListeners() {
        binding.btnFetchMessages.setOnClickListener {
            fetchMessages()
        }
        
        binding.btnFetchAndSend.setOnClickListener {
            fetchAndSendMessages()
        }
    }
    
    private fun checkPermissions() {
        when {
            ActivityCompat.checkSelfPermission(
                this,
                Manifest.permission.SEND_SMS
            ) == PackageManager.PERMISSION_GRANTED -> {
                // Permission already granted
            }
            ActivityCompat.shouldShowRequestPermissionRationale(
                this,
                Manifest.permission.SEND_SMS
            ) -> {
                Toast.makeText(
                    this,
                    "SMS permission is required to send messages",
                    Toast.LENGTH_LONG
                ).show()
                requestPermissionLauncher.launch(Manifest.permission.SEND_SMS)
            }
            else -> {
                requestPermissionLauncher.launch(Manifest.permission.SEND_SMS)
            }
        }
    }
    
    private fun fetchMessages() {
        lifecycleScope.launch {
            try {
                binding.progressBar.visibility = android.view.View.VISIBLE
                val messages = messageRepository.fetchMessages()
                messageAdapter.updateMessages(messages)
                binding.progressBar.visibility = android.view.View.GONE
                
                Toast.makeText(
                    this@MainActivity,
                    "Fetched ${messages.size} messages",
                    Toast.LENGTH_SHORT
                ).show()
            } catch (e: Exception) {
                binding.progressBar.visibility = android.view.View.GONE
                Toast.makeText(
                    this@MainActivity,
                    "Error fetching messages: ${e.message}",
                    Toast.LENGTH_LONG
                ).show()
            }
        }
    }
    
    private fun fetchAndSendMessages() {
        lifecycleScope.launch {
            try {
                binding.progressBar.visibility = android.view.View.VISIBLE
                val messages = messageRepository.fetchAndSendMessages()
                messageAdapter.updateMessages(messages)
                binding.progressBar.visibility = android.view.View.GONE
                
                val sentCount = messages.count { it.sent }
                val totalCount = messages.size
                
                Toast.makeText(
                    this@MainActivity,
                    "Processed $totalCount messages, sent $sentCount SMS",
                    Toast.LENGTH_LONG
                ).show()
            } catch (e: Exception) {
                binding.progressBar.visibility = android.view.View.GONE
                Toast.makeText(
                    this@MainActivity,
                    "Error processing messages: ${e.message}",
                    Toast.LENGTH_LONG
                ).show()
            }
        }
    }
}