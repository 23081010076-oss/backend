# Frontend Integration Guide

## 📋 API Response Format

Semua endpoint API menggunakan format response yang **konsisten** untuk memudahkan frontend integration.

---

## ✅ Success Response

### Format Standar
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Resource data here
  }
}
```

### Contoh: Get Single Resource
```json
{
  "success": true,
  "message": "Subscription retrieved successfully",
  "data": {
    "id": 1,
    "plan": "premium",
    "status": "active",
    "user_id": 123
  }
}
```

### Contoh: Create Resource (201)
```json
{
  "success": true,
  "message": "Subscription created successfully",
  "data": {
    "id": 5,
    "plan": "regular",
    "status": "active"
  }
}
```

### Contoh: Delete Resource
```json
{
  "success": true,
  "message": "Subscription deleted successfully",
  "data": null
}
```

---

## 📄 Paginated Response

### Format
```json
{
  "success": true,
  "message": "Subscriptions retrieved successfully",
  "data": [
    { "id": 1, "plan": "premium" },
    { "id": 2, "plan": "regular" }
  ],
  "meta": {
    "total": 100,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7,
    "from": 1,
    "to": 15
  }
}
```

### Frontend Access
```javascript
// Axios example
const response = await axios.get('/api/subscriptions');

// Access data
const items = response.data.data; // Array of items
const pagination = response.data.meta;

// Pagination info
console.log(`Showing ${pagination.from}-${pagination.to} of ${pagination.total}`);
console.log(`Page ${pagination.current_page} of ${pagination.last_page}`);
```

---

## ❌ Error Response

### Format Standar
```json
{
  "success": false,
  "message": "Error message",
  "data": null,
  "errors": {
    // Optional error details
  }
}
```

### Contoh: Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "data": null,
  "errors": {
    "error": "You have already reviewed this item"
  }
}
```

### Contoh: Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthorized",
  "data": null
}
```

### Contoh: Forbidden (403)
```json
{
  "success": false,
  "message": "Forbidden",
  "data": null
}
```

### Contoh: Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found",
  "data": null
}
```

### Contoh: Server Error (500)
```json
{
  "success": false,
  "message": "Internal server error",
  "data": null
}
```

---

## 🎯 Frontend Best Practices

### 1. Always Check `success` Flag
```javascript
const response = await axios.get('/api/subscriptions');

if (response.data.success) {
  // Handle success
  const data = response.data.data;
  console.log('Data:', data);
} else {
  // Handle error
  console.error('Error:', response.data.message);
}
```

### 2. Consistent Error Handling
```javascript
try {
  const response = await axios.post('/api/subscriptions', formData);
  
  if (response.data.success) {
    // Success
    showSuccessMessage(response.data.message);
    return response.data.data;
  }
} catch (error) {
  // Network error or HTTP error status
  if (error.response) {
    // Server responded with error
    const errorData = error.response.data;
    showErrorMessage(errorData.message);
    
    // Show validation errors if present
    if (errorData.errors) {
      displayValidationErrors(errorData.errors);
    }
  } else {
    // Network error
    showErrorMessage('Network error. Please try again.');
  }
}
```

### 3. Pagination Helper
```javascript
function PaginationInfo({ meta }) {
  return (
    <div>
      <p>Showing {meta.from}-{meta.to} of {meta.total} items</p>
      <p>Page {meta.current_page} of {meta.last_page}</p>
    </div>
  );
}

// Usage
<PaginationInfo meta={response.data.meta} />
```

### 4. Generic API Service
```javascript
class ApiService {
  async get(endpoint) {
    try {
      const response = await axios.get(endpoint);
      return this.handleResponse(response);
    } catch (error) {
      return this.handleError(error);
    }
  }

  async post(endpoint, data) {
    try {
      const response = await axios.post(endpoint, data);
      return this.handleResponse(response);
    } catch (error) {
      return this.handleError(error);
    }
  }

  handleResponse(response) {
    if (response.data.success) {
      return {
        success: true,
        data: response.data.data,
        message: response.data.message,
        meta: response.data.meta // For pagination
      };
    }
    return {
      success: false,
      message: response.data.message,
      errors: response.data.errors
    };
  }

  handleError(error) {
    if (error.response) {
      return {
        success: false,
        message: error.response.data.message || 'An error occurred',
        errors: error.response.data.errors
      };
    }
    return {
      success: false,
      message: 'Network error. Please try again.'
    };
  }
}

// Usage
const api = new ApiService();
const result = await api.get('/api/subscriptions');

if (result.success) {
  console.log('Data:', result.data);
} else {
  console.error('Error:', result.message);
}
```

---

## 📝 Key Points

### ✅ Advantages of This Format

1. **Consistent Structure**: `success`, `message`, `data` always present
2. **Easy to Check**: Just check `response.data.success`
3. **Always Has Data Key**: No need to check if `data` exists - it's always there (null if no data)
4. **Clear Pagination**: `meta` object contains all pagination info
5. **Descriptive Messages**: Every response has a human-readable message
6. **Error Details**: Validation errors in `errors` object

### 🎨 Frontend Access Pattern

```javascript
// ✅ GOOD - Consistent access
const data = response.data.data; // Always works

// ❌ BAD - Old inconsistent way
const data = response.data.data || response.data || response.data.items;
```

### 🔄 Migration from Old Format

If you have old code expecting different format:

```javascript
// Old format
response.data // Direct data

// New format
response.data.data // Data is nested

// Quick fix wrapper
function getData(response) {
  return response.data.data;
}
```

---

## 🚀 Example: Complete CRUD Operations

```javascript
// List with pagination
const listSubscriptions = async (page = 1) => {
  const response = await axios.get(`/api/subscriptions?page=${page}`);
  return {
    items: response.data.data,
    pagination: response.data.meta
  };
};

// Get single
const getSubscription = async (id) => {
  const response = await axios.get(`/api/subscriptions/${id}`);
  return response.data.data;
};

// Create
const createSubscription = async (formData) => {
  const response = await axios.post('/api/subscriptions', formData);
  if (response.data.success) {
    return response.data.data;
  }
  throw new Error(response.data.message);
};

// Update
const updateSubscription = async (id, formData) => {
  const response = await axios.put(`/api/subscriptions/${id}`, formData);
  if (response.data.success) {
    return response.data.data;
  }
  throw new Error(response.data.message);
};

// Delete
const deleteSubscription = async (id) => {
  const response = await axios.delete(`/api/subscriptions/${id}`);
  return response.data.success;
};
```

---

## 💡 Tips

1. **Always use `response.data.data`** untuk mengakses actual data
2. **Check `response.data.success`** sebelum process data
3. **Use `response.data.message`** untuk user feedback
4. **Access pagination via `response.data.meta`**
5. **Handle errors dengan `response.data.errors`**

Happy coding! 🎉
