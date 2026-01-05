# Frontend Implementation Guide - Course Recommendation System

## 📋 Table of Contents
1. [Overview](#overview)
2. [API Endpoints](#api-endpoints)
3. [React Implementation](#react-implementation)
4. [Vue.js Implementation](#vuejs-implementation)
5. [Angular Implementation](#angular-implementation)
6. [Vanilla JavaScript](#vanilla-javascript)
7. [UI/UX Best Practices](#uiux-best-practices)

---

## Overview

Sistem rekomendasi course menggunakan data profile user (specialization & major) untuk memberikan rekomendasi yang dipersonalisasi.

### Flow Implementasi:
1. **Update Profile** - User mengisi specialization (minat) dan major (jurusan)
2. **Get Recommendations** - Sistem memberikan rekomendasi berdasarkan profile
3. **Display Courses** - Tampilkan course dengan relevance score

---

## API Endpoints

### Base URL
```
http://127.0.0.1:8000/api
```

### 1. Update Profile (PUT /api/auth/profile)

**Headers:**
```json
{
  "Authorization": "Bearer YOUR_JWT_TOKEN",
  "Content-Type": "application/json"
}
```

**Request Body:**
```json
{
  "major": "Teknik Informatika",
  "specialization": [
    "Web Development",
    "React",
    "UI/UX Design"
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Profil berhasil diupdate",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "major": "Teknik Informatika",
    "specialization": ["Web Development", "React", "UI/UX Design"],
    "role": "student"
  }
}
```

### 2. Get Recommendations (GET /api/auth/recommendations)

**Headers:**
```json
{
  "Authorization": "Bearer YOUR_JWT_TOKEN"
}
```

**Query Parameters:**
```
limit: integer (default: 5, optional)
```

**Response:**
```json
{
  "success": true,
  "message": "Rekomendasi kursus berhasil diambil",
  "data": {
    "recommendations": [
      {
        "id": 1,
        "title": "React Advanced Development",
        "image": "http://127.0.0.1:8000/storage/courses/react.jpg",
        "description": "Master React with hooks...",
        "category": "Web Development",
        "level": "intermediate",
        "duration": "8 minggu",
        "price": 1500000.00,
        "access_type": "premium",
        "instructor": "Sarah Johnson",
        "enrollments_count": 245,
        "reviews_avg_rating": 4.8,
        "average_rating": 4.8,
        "total_reviews": 89,
        "total_materials": 42,
        "total_curriculum_duration": "12 jam 30 menit",
        "relevance_score": 150
      }
    ],
    "criteria": {
      "subscription_plan": "premium",
      "specializations": ["Web Development", "React"],
      "major": "Teknik Informatika",
      "excluded_enrolled": 3,
      "algorithm": "specialization_score + major_score + rating + popularity"
    }
  }
}
```

---

## React Implementation

### 1. Setup API Service

```javascript
// services/api.js
import axios from 'axios';

const API_BASE_URL = 'http://127.0.0.1:8000/api';

// Create axios instance
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export const profileAPI = {
  // Update profile with specialization and major
  updateProfile: (data) => api.put('/auth/profile', data),
  
  // Get course recommendations
  getRecommendations: (limit = 5) => 
    api.get('/auth/recommendations', { params: { limit } }),
};

export default api;
```

### 2. Profile Update Component

```javascript
// components/ProfileForm.jsx
import React, { useState } from 'react';
import { profileAPI } from '../services/api';

const ProfileForm = ({ user, onUpdate }) => {
  const [formData, setFormData] = useState({
    major: user?.major || '',
    specialization: user?.specialization || [],
  });
  const [newSpecialization, setNewSpecialization] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const addSpecialization = () => {
    if (newSpecialization.trim() && !formData.specialization.includes(newSpecialization)) {
      setFormData({
        ...formData,
        specialization: [...formData.specialization, newSpecialization.trim()],
      });
      setNewSpecialization('');
    }
  };

  const removeSpecialization = (spec) => {
    setFormData({
      ...formData,
      specialization: formData.specialization.filter(s => s !== spec),
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      const response = await profileAPI.updateProfile(formData);
      onUpdate(response.data.data);
      alert('Profile updated successfully!');
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to update profile');
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="profile-form">
      <h2>Update Your Profile</h2>
      
      {error && <div className="alert alert-danger">{error}</div>}

      {/* Major Field */}
      <div className="form-group">
        <label htmlFor="major">Major / Jurusan</label>
        <input
          type="text"
          id="major"
          className="form-control"
          value={formData.major}
          onChange={(e) => setFormData({ ...formData, major: e.target.value })}
          placeholder="e.g., Teknik Informatika"
        />
      </div>

      {/* Specialization Field */}
      <div className="form-group">
        <label>Specialization / Minat</label>
        <div className="specialization-input">
          <input
            type="text"
            className="form-control"
            value={newSpecialization}
            onChange={(e) => setNewSpecialization(e.target.value)}
            onKeyPress={(e) => e.key === 'Enter' && (e.preventDefault(), addSpecialization())}
            placeholder="Add your interest (e.g., Web Development)"
          />
          <button type="button" onClick={addSpecialization} className="btn btn-secondary">
            Add
          </button>
        </div>

        {/* Display Specializations as Tags */}
        <div className="specialization-tags">
          {formData.specialization.map((spec, index) => (
            <span key={index} className="badge badge-primary">
              {spec}
              <button
                type="button"
                onClick={() => removeSpecialization(spec)}
                className="btn-close"
              >
                ×
              </button>
            </span>
          ))}
        </div>
      </div>

      <button type="submit" className="btn btn-primary" disabled={loading}>
        {loading ? 'Updating...' : 'Update Profile'}
      </button>
    </form>
  );
};

export default ProfileForm;
```

### 3. Course Recommendations Component

```javascript
// components/CourseRecommendations.jsx
import React, { useState, useEffect } from 'react';
import { profileAPI } from '../services/api';

const CourseRecommendations = () => {
  const [recommendations, setRecommendations] = useState([]);
  const [criteria, setCriteria] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchRecommendations();
  }, []);

  const fetchRecommendations = async (limit = 5) => {
    setLoading(true);
    setError(null);

    try {
      const response = await profileAPI.getRecommendations(limit);
      const { recommendations, criteria } = response.data.data;
      
      setRecommendations(recommendations);
      setCriteria(criteria);
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to fetch recommendations');
    } finally {
      setLoading(false);
    }
  };

  const getRelevanceBadge = (score) => {
    if (!score) return null;
    if (score >= 100) return <span className="badge badge-success">Highly Relevant</span>;
    if (score >= 50) return <span className="badge badge-info">Relevant</span>;
    return <span className="badge badge-secondary">Related</span>;
  };

  const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
    }).format(price);
  };

  if (loading) {
    return <div className="spinner">Loading recommendations...</div>;
  }

  if (error) {
    return <div className="alert alert-danger">{error}</div>;
  }

  return (
    <div className="course-recommendations">
      <div className="header">
        <h2>🎯 Recommended for You</h2>
        {criteria && (
          <div className="criteria-info">
            <p>
              Based on your interests: 
              <strong> {criteria.specializations?.join(', ') || 'Not specified'}</strong>
            </p>
            {criteria.major && (
              <p>Major: <strong>{criteria.major}</strong></p>
            )}
            <p className="text-muted">
              {criteria.excluded_enrolled} courses already enrolled
            </p>
          </div>
        )}
      </div>

      <div className="recommendations-grid">
        {recommendations.length === 0 ? (
          <div className="empty-state">
            <p>No recommendations available. Try updating your profile with interests!</p>
          </div>
        ) : (
          recommendations.map((course) => (
            <div key={course.id} className="course-card">
              {/* Relevance Badge */}
              {course.relevance_score && (
                <div className="relevance-indicator">
                  {getRelevanceBadge(course.relevance_score)}
                  <span className="score">Score: {course.relevance_score}</span>
                </div>
              )}

              {/* Course Image */}
              <img 
                src={course.image || '/placeholder-course.jpg'} 
                alt={course.title}
                className="course-image"
              />

              {/* Course Info */}
              <div className="course-content">
                <div className="course-header">
                  <span className="badge badge-category">{course.category}</span>
                  <span className="badge badge-level">{course.level}</span>
                </div>

                <h3>{course.title}</h3>
                <p className="description">{course.description}</p>

                <div className="course-meta">
                  <div className="meta-item">
                    <span className="icon">👨‍🏫</span>
                    <span>{course.instructor}</span>
                  </div>
                  <div className="meta-item">
                    <span className="icon">⏱️</span>
                    <span>{course.duration}</span>
                  </div>
                  <div className="meta-item">
                    <span className="icon">📚</span>
                    <span>{course.total_materials} materials</span>
                  </div>
                </div>

                {/* Rating */}
                <div className="course-rating">
                  <span className="stars">⭐ {course.average_rating.toFixed(1)}</span>
                  <span className="reviews">({course.total_reviews} reviews)</span>
                  <span className="enrollments">• {course.enrollments_count} students</span>
                </div>

                {/* Price & Action */}
                <div className="course-footer">
                  <div className="price">
                    {course.access_type === 'free' ? (
                      <span className="free">FREE</span>
                    ) : (
                      <span className="amount">{formatPrice(course.price)}</span>
                    )}
                    <span className="access-type">{course.access_type}</span>
                  </div>
                  <button className="btn btn-primary">
                    View Details
                  </button>
                </div>
              </div>
            </div>
          ))
        )}
      </div>

      {/* Load More */}
      {recommendations.length > 0 && (
        <div className="load-more">
          <button 
            className="btn btn-outline-primary"
            onClick={() => fetchRecommendations(recommendations.length + 5)}
          >
            Load More
          </button>
        </div>
      )}
    </div>
  );
};

export default CourseRecommendations;
```

### 4. Custom Hook for Recommendations

```javascript
// hooks/useRecommendations.js
import { useState, useEffect } from 'react';
import { profileAPI } from '../services/api';

export const useRecommendations = (limit = 5, autoFetch = true) => {
  const [recommendations, setRecommendations] = useState([]);
  const [criteria, setCriteria] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const fetchRecommendations = async (newLimit = limit) => {
    setLoading(true);
    setError(null);

    try {
      const response = await profileAPI.getRecommendations(newLimit);
      const { recommendations, criteria } = response.data.data;
      
      setRecommendations(recommendations);
      setCriteria(criteria);
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to fetch recommendations');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (autoFetch) {
      fetchRecommendations();
    }
  }, [autoFetch]);

  return {
    recommendations,
    criteria,
    loading,
    error,
    refetch: fetchRecommendations,
  };
};
```

### 5. Usage Example

```javascript
// pages/Dashboard.jsx
import React from 'react';
import ProfileForm from '../components/ProfileForm';
import CourseRecommendations from '../components/CourseRecommendations';
import { useRecommendations } from '../hooks/useRecommendations';

const Dashboard = () => {
  const { recommendations, criteria, loading, refetch } = useRecommendations();

  const handleProfileUpdate = (updatedUser) => {
    console.log('Profile updated:', updatedUser);
    // Refetch recommendations after profile update
    refetch();
  };

  return (
    <div className="dashboard">
      <ProfileForm onUpdate={handleProfileUpdate} />
      <CourseRecommendations />
    </div>
  );
};

export default Dashboard;
```

---

## Vue.js Implementation

### 1. API Service

```javascript
// services/api.js
import axios from 'axios';

const API_BASE_URL = 'http://127.0.0.1:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default {
  profile: {
    update: (data) => api.put('/auth/profile', data),
    getRecommendations: (limit = 5) => 
      api.get('/auth/recommendations', { params: { limit } }),
  },
};
```

### 2. Profile Update Component

```vue
<!-- components/ProfileForm.vue -->
<template>
  <div class="profile-form">
    <h2>Update Your Profile</h2>
    
    <form @submit.prevent="submitProfile">
      <div v-if="error" class="alert alert-danger">{{ error }}</div>

      <!-- Major -->
      <div class="form-group">
        <label for="major">Major / Jurusan</label>
        <input
          id="major"
          v-model="formData.major"
          type="text"
          class="form-control"
          placeholder="e.g., Teknik Informatika"
        />
      </div>

      <!-- Specialization -->
      <div class="form-group">
        <label>Specialization / Minat</label>
        <div class="specialization-input">
          <input
            v-model="newSpecialization"
            type="text"
            class="form-control"
            placeholder="Add your interest"
            @keypress.enter.prevent="addSpecialization"
          />
          <button type="button" @click="addSpecialization" class="btn btn-secondary">
            Add
          </button>
        </div>

        <div class="specialization-tags">
          <span
            v-for="(spec, index) in formData.specialization"
            :key="index"
            class="badge badge-primary"
          >
            {{ spec }}
            <button type="button" @click="removeSpecialization(spec)" class="btn-close">
              ×
            </button>
          </span>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" :disabled="loading">
        {{ loading ? 'Updating...' : 'Update Profile' }}
      </button>
    </form>
  </div>
</template>

<script>
import api from '../services/api';

export default {
  name: 'ProfileForm',
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      formData: {
        major: this.user?.major || '',
        specialization: this.user?.specialization || [],
      },
      newSpecialization: '',
      loading: false,
      error: null,
    };
  },
  methods: {
    addSpecialization() {
      const spec = this.newSpecialization.trim();
      if (spec && !this.formData.specialization.includes(spec)) {
        this.formData.specialization.push(spec);
        this.newSpecialization = '';
      }
    },
    removeSpecialization(spec) {
      this.formData.specialization = this.formData.specialization.filter(
        s => s !== spec
      );
    },
    async submitProfile() {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.profile.update(this.formData);
        this.$emit('update', response.data.data);
        alert('Profile updated successfully!');
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to update profile';
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
```

### 3. Course Recommendations Component

```vue
<!-- components/CourseRecommendations.vue -->
<template>
  <div class="course-recommendations">
    <div class="header">
      <h2>🎯 Recommended for You</h2>
      <div v-if="criteria" class="criteria-info">
        <p>
          Based on your interests:
          <strong>{{ criteria.specializations?.join(', ') || 'Not specified' }}</strong>
        </p>
        <p v-if="criteria.major">Major: <strong>{{ criteria.major }}</strong></p>
      </div>
    </div>

    <div v-if="loading" class="spinner">Loading recommendations...</div>
    <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
    <div v-else class="recommendations-grid">
      <div
        v-for="course in recommendations"
        :key="course.id"
        class="course-card"
      >
        <!-- Relevance Badge -->
        <div v-if="course.relevance_score" class="relevance-indicator">
          <span :class="getRelevanceBadgeClass(course.relevance_score)">
            {{ getRelevanceLabel(course.relevance_score) }}
          </span>
          <span class="score">Score: {{ course.relevance_score }}</span>
        </div>

        <img :src="course.image || '/placeholder-course.jpg'" :alt="course.title" />

        <div class="course-content">
          <div class="course-header">
            <span class="badge badge-category">{{ course.category }}</span>
            <span class="badge badge-level">{{ course.level }}</span>
          </div>

          <h3>{{ course.title }}</h3>
          <p>{{ course.description }}</p>

          <div class="course-meta">
            <div class="meta-item">
              <span>👨‍🏫 {{ course.instructor }}</span>
            </div>
            <div class="meta-item">
              <span>⏱️ {{ course.duration }}</span>
            </div>
          </div>

          <div class="course-rating">
            <span>⭐ {{ course.average_rating.toFixed(1) }}</span>
            <span>({{ course.total_reviews }} reviews)</span>
            <span>• {{ course.enrollments_count }} students</span>
          </div>

          <div class="course-footer">
            <div class="price">
              <span v-if="course.access_type === 'free'" class="free">FREE</span>
              <span v-else>{{ formatPrice(course.price) }}</span>
            </div>
            <button class="btn btn-primary">View Details</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';

export default {
  name: 'CourseRecommendations',
  data() {
    return {
      recommendations: [],
      criteria: null,
      loading: true,
      error: null,
    };
  },
  mounted() {
    this.fetchRecommendations();
  },
  methods: {
    async fetchRecommendations(limit = 5) {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.profile.getRecommendations(limit);
        this.recommendations = response.data.data.recommendations;
        this.criteria = response.data.data.criteria;
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to fetch recommendations';
      } finally {
        this.loading = false;
      }
    },
    getRelevanceLabel(score) {
      if (score >= 100) return 'Highly Relevant';
      if (score >= 50) return 'Relevant';
      return 'Related';
    },
    getRelevanceBadgeClass(score) {
      if (score >= 100) return 'badge badge-success';
      if (score >= 50) return 'badge badge-info';
      return 'badge badge-secondary';
    },
    formatPrice(price) {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
      }).format(price);
    },
  },
};
</script>
```

---

## Angular Implementation

### 1. API Service

```typescript
// services/api.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

interface ProfileData {
  major: string;
  specialization: string[];
}

interface RecommendationResponse {
  success: boolean;
  message: string;
  data: {
    recommendations: any[];
    criteria: any;
  };
}

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private baseURL = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return new HttpHeaders({
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    });
  }

  updateProfile(data: ProfileData): Observable<any> {
    return this.http.put(
      `${this.baseURL}/auth/profile`,
      data,
      { headers: this.getHeaders() }
    );
  }

  getRecommendations(limit: number = 5): Observable<RecommendationResponse> {
    const params = new HttpParams().set('limit', limit.toString());
    return this.http.get<RecommendationResponse>(
      `${this.baseURL}/auth/recommendations`,
      { headers: this.getHeaders(), params }
    );
  }
}
```

### 2. Profile Component

```typescript
// components/profile-form/profile-form.component.ts
import { Component, OnInit, Output, EventEmitter } from '@angular/core';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-profile-form',
  templateUrl: './profile-form.component.html',
  styleUrls: ['./profile-form.component.css']
})
export class ProfileFormComponent implements OnInit {
  @Output() profileUpdated = new EventEmitter<any>();

  formData = {
    major: '',
    specialization: [] as string[]
  };
  newSpecialization = '';
  loading = false;
  error: string | null = null;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {}

  addSpecialization(): void {
    const spec = this.newSpecialization.trim();
    if (spec && !this.formData.specialization.includes(spec)) {
      this.formData.specialization.push(spec);
      this.newSpecialization = '';
    }
  }

  removeSpecialization(spec: string): void {
    this.formData.specialization = this.formData.specialization.filter(
      s => s !== spec
    );
  }

  onSubmit(): void {
    this.loading = true;
    this.error = null;

    this.apiService.updateProfile(this.formData).subscribe({
      next: (response) => {
        this.profileUpdated.emit(response.data);
        alert('Profile updated successfully!');
        this.loading = false;
      },
      error: (err) => {
        this.error = err.error?.message || 'Failed to update profile';
        this.loading = false;
      }
    });
  }
}
```

---

## Vanilla JavaScript

```javascript
// main.js
const API_BASE_URL = 'http://127.0.0.1:8000/api';

// Helper: Get token from localStorage
const getToken = () => localStorage.getItem('token');

// Helper: Make API request
async function apiRequest(endpoint, options = {}) {
  const token = getToken();
  const headers = {
    'Content-Type': 'application/json',
    ...(token && { 'Authorization': `Bearer ${token}` }),
    ...options.headers,
  };

  const response = await fetch(`${API_BASE_URL}${endpoint}`, {
    ...options,
    headers,
  });

  if (!response.ok) {
    throw new Error(await response.text());
  }

  return response.json();
}

// Update Profile
async function updateProfile(data) {
  return apiRequest('/auth/profile', {
    method: 'PUT',
    body: JSON.stringify(data),
  });
}

// Get Recommendations
async function getRecommendations(limit = 5) {
  return apiRequest(`/auth/recommendations?limit=${limit}`);
}

// Display Recommendations
function displayRecommendations(recommendations) {
  const container = document.getElementById('recommendations-container');
  container.innerHTML = '';

  recommendations.forEach(course => {
    const card = document.createElement('div');
    card.className = 'course-card';
    card.innerHTML = `
      <div class="relevance-score">${course.relevance_score || 0}</div>
      <img src="${course.image}" alt="${course.title}">
      <h3>${course.title}</h3>
      <p>${course.description}</p>
      <div class="rating">⭐ ${course.average_rating.toFixed(1)}</div>
      <button onclick="enrollCourse(${course.id})">View Details</button>
    `;
    container.appendChild(card);
  });
}

// Initialize
document.addEventListener('DOMContentLoaded', async () => {
  try {
    const response = await getRecommendations(5);
    displayRecommendations(response.data.recommendations);
  } catch (error) {
    console.error('Failed to load recommendations:', error);
  }
});
```

---

## UI/UX Best Practices

### 1. **Onboarding Flow**
```
Register → Login → Complete Profile (Specialization) → View Recommendations
```

### 2. **Visual Indicators**
- **Relevance Score Badge**: Show "Highly Relevant", "Relevant", "Related"
- **Color Coding**: Green (>100), Blue (50-99), Gray (<50)
- **Star Rating**: Prominently display average rating

### 3. **Empty States**
```javascript
if (recommendations.length === 0) {
  return (
    <div className="empty-state">
      <h3>No recommendations yet!</h3>
      <p>Add your interests to get personalized course recommendations</p>
      <button onClick={() => navigate('/profile')}>Update Profile</button>
    </div>
  );
}
```

### 4. **Loading States**
```javascript
{loading && (
  <div className="skeleton-loader">
    {[1, 2, 3, 4, 5].map(i => (
      <div key={i} className="skeleton-card"></div>
    ))}
  </div>
)}
```

### 5. **Error Handling**
```javascript
if (error) {
  return (
    <div className="alert alert-warning">
      <p>Couldn't load recommendations</p>
      <button onClick={refetch}>Try Again</button>
    </div>
  );
}
```

### 6. **Responsive Design**
```css
.recommendations-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

@media (max-width: 768px) {
  .recommendations-grid {
    grid-template-columns: 1fr;
  }
}
```

---

## Testing

### Test Update Profile
```javascript
// Test data
const testProfile = {
  major: "Teknik Informatika",
  specialization: ["Web Development", "React", "Node.js"]
};

profileAPI.updateProfile(testProfile)
  .then(res => console.log('✅ Profile updated:', res.data))
  .catch(err => console.error('❌ Error:', err));
```

### Test Get Recommendations
```javascript
profileAPI.getRecommendations(10)
  .then(res => {
    console.log('✅ Recommendations:', res.data.data.recommendations);
    console.log('📊 Criteria:', res.data.data.criteria);
  })
  .catch(err => console.error('❌ Error:', err));
```

---

## Deployment Notes

1. **Update Base URL** in production:
   ```javascript
   const API_BASE_URL = process.env.REACT_APP_API_URL || 'https://api.yourdomain.com/api';
   ```

2. **CORS Configuration**: Ensure backend allows frontend domain

3. **Token Management**: Implement refresh token logic

4. **Error Boundaries**: Wrap components in error boundaries

---

## Summary

✅ **API Endpoints**: 2 endpoints (update profile, get recommendations)
✅ **React**: Complete with hooks & components
✅ **Vue.js**: Complete with composition API
✅ **Angular**: Complete with services & components
✅ **Vanilla JS**: Simple fetch-based implementation
✅ **Best Practices**: Loading states, error handling, responsive design

📚 **Next Steps**:
1. Test API endpoints with Postman
2. Implement UI components
3. Add loading/error states
4. Test user flows
5. Deploy to production
