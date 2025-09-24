# SMS Messenger Android App

An Android application that fetches messages from a web server API and sends them via SMS.

## Features

- **API Integration**: Fetches messages from a REST API endpoint
- **SMS Sending**: Sends SMS messages to recipients
- **Permission Handling**: Requests and manages SMS permissions
- **Background Sync**: Runs background service for periodic message checking
- **Modern UI**: Material Design interface with RecyclerView for message display
- **Error Handling**: Comprehensive error handling and user feedback

## Setup Instructions

### 1. Server Configuration

Update the `BASE_URL` in `NetworkModule.kt` with your actual server endpoint:

```kotlin
private const val BASE_URL = "http://your-server.com/api/"
```

### 2. Server API Endpoints

Your server should implement these endpoints:

- `GET /messages` - Returns list of messages
- `GET /messages/{id}` - Returns specific message
- `POST /messages/{id}/sent` - Marks message as sent

### 3. Message Format

The API should return messages in this JSON format:

```json
{
  "id": "unique_message_id",
  "content": "SMS message content",
  "recipient": "phone_number",
  "timestamp": 1640995200000,
  "sent": false
}
```

### 4. Permissions

The app requires these permissions:
- `SEND_SMS` - To send SMS messages
- `READ_SMS` - To read SMS (optional)
- `INTERNET` - To fetch messages from server
- `ACCESS_NETWORK_STATE` - To check network connectivity

## Usage

1. **Manual Operation**:
   - Tap "Fetch Messages" to retrieve messages without sending
   - Tap "Fetch & Send SMS" to fetch and automatically send SMS

2. **Background Sync**:
   - The app includes a background service that checks for new messages every 5 minutes
   - Messages are automatically fetched and sent when available

3. **Permission Handling**:
   - The app will request SMS permission on first launch
   - Users can grant/deny permission through the system dialog

## Architecture

### Key Components

- **MainActivity**: Main UI with message list and controls
- **MessageRepository**: Handles API calls and SMS operations
- **SmsManager**: Manages SMS sending functionality
- **MessageSyncService**: Background service for periodic sync
- **ApiService**: Retrofit interface for API communication
- **MessageAdapter**: RecyclerView adapter for message display

### Network Stack

- **Retrofit**: HTTP client for API communication
- **Gson**: JSON serialization/deserialization
- **OkHttp**: HTTP client with logging interceptor

### Background Processing

- **WorkManager**: Handles background sync tasks
- **Foreground Service**: Ensures sync continues when app is backgrounded

## Building and Running

1. Open the project in Android Studio
2. Update the server URL in `NetworkModule.kt`
3. Build and run on an Android device or emulator
4. Grant SMS permissions when prompted

## Testing

### Manual Testing

1. Set up a test server with the required API endpoints
2. Create test messages in your server
3. Run the app and test both manual and automatic sync
4. Verify SMS messages are sent correctly

### Server Requirements

Your server should handle:
- CORS headers for web requests
- Proper JSON responses
- Message status updates (sent/unsent)
- Rate limiting for API calls

## Security Considerations

- Store sensitive data securely
- Use HTTPS in production
- Validate phone numbers before sending SMS
- Implement proper authentication if needed
- Consider rate limiting for SMS sending

## Troubleshooting

### Common Issues

1. **SMS Permission Denied**: Check device settings and re-grant permission
2. **Network Errors**: Verify server URL and network connectivity
3. **Messages Not Sending**: Check phone number format and SMS service availability
4. **Background Sync Not Working**: Ensure battery optimization is disabled for the app

### Debug Tips

- Check Logcat for detailed error messages
- Verify API responses in network logs
- Test with a simple message first
- Ensure device has SMS capability

## Dependencies

- AndroidX libraries
- Retrofit 2.9.0
- Gson converter
- OkHttp with logging
- WorkManager for background tasks
- Material Design components