# API Endpoints Testing Guide

## 🚀 New Polling Endpoints

### Student Data Endpoint
```
GET /api/student/data
```

**Headers:**
```
Authorization: Bearer {your-sanctum-token}
Accept: application/json
```

**Response Structure:**
```json
{
  "student": {
    "id": 123,
    "fname": "John",
    "lname": "Doe", 
    "email": "john@example.com"
  },
  "course_auths": [
    {
      "id": 1,
      "course_id": 45,
      "user_id": 123,
      "status": "enrolled",
      "progress": 75,
      "created_at": "2025-09-01T10:00:00Z",
      "updated_at": "2025-09-11T14:30:00Z",
      "course": {
        "id": 45,
        "title": "Advanced Network Security",
        "description": "Learn advanced network security concepts",
        "slug": "advanced-network-security"
      }
    }
  ]
}
```

### Classroom Data Endpoint
```
GET /api/classroom/data
```

**Headers:**
```
Authorization: Bearer {your-sanctum-token}
Accept: application/json
```

**Response Structure:**
```json
{
  "instructor": {
    "id": 67,
    "fname": "Dr. Sarah",
    "lname": "Johnson",
    "email": "sarah.johnson@security.edu"
  },
  "course_dates": [
    {
      "id": 123,
      "course_id": 45,
      "start_date": "2025-09-15",
      "end_date": "2025-09-20",
      "session_date": "2025-09-15T09:00:00Z"
    }
  ]
}
```

## 🧪 Testing Commands

### Using cURL:

**Test Student Data:**
```bash
curl -X GET "https://frost.test/api/student/data" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Test Classroom Data:**
```bash
curl -X GET "https://frost.test/api/classroom/data" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Using JavaScript (Browser Console):

```javascript
// Test Student Data
fetch('/api/student/data', {
  headers: {
    'Authorization': 'Bearer ' + document.querySelector('meta[name="csrf-token"]').content,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log('Student Data:', data));

// Test Classroom Data  
fetch('/api/classroom/data', {
  headers: {
    'Authorization': 'Bearer ' + document.querySelector('meta[name="csrf-token"]').content,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log('Classroom Data:', data));
```

## 📊 Rate Limits

- **Student Endpoints:** 60 requests per minute
- **Classroom Endpoints:** 60 requests per minute
- **Authentication:** Required for all endpoints

## 🔄 Polling Recommendations

Based on `config/endpoints.php`:
- **Student Data:** Poll every 30 seconds
- **Classroom Data:** Poll every 45 seconds
- **Implement exponential backoff** on errors
- **Maximum 3 retry attempts** before fallback

## ✅ Success Indicators

**Endpoint Working Correctly:**
- Status Code: 200
- Valid JSON response
- Data matches TypeScript interfaces
- No authentication errors

**Common Issues:**
- 401: Authentication required
- 429: Rate limit exceeded  
- 500: Server error (check logs)

## 🚀 Next Steps

1. **Test endpoints manually** using cURL or browser
2. **Implement React polling** using these endpoints
3. **Add error handling** for network issues
4. **Set up retry logic** with exponential backoff
5. **Monitor rate limits** in production
