# 🚀 Quick Start - Frontend Integration

## TL;DR - Copy Paste Ready

### 1️⃣ Update User Profile with Specialization

```javascript
// JavaScript/React
const updateProfile = async () => {
  const response = await fetch('http://127.0.0.1:8000/api/auth/profile', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${yourToken}`
    },
    body: JSON.stringify({
      major: "Teknik Informatika",
      specialization: ["Web Development", "React", "UI/UX Design"]
    })
  });
  
  const data = await response.json();
  console.log(data);
};
```

### 2️⃣ Get Course Recommendations

```javascript
// JavaScript/React
const getRecommendations = async () => {
  const response = await fetch('http://127.0.0.1:8000/api/auth/recommendations?limit=5', {
    headers: {
      'Authorization': `Bearer ${yourToken}`
    }
  });
  
  const data = await response.json();
  console.log(data.data.recommendations);
  console.log(data.data.criteria);
};
```

---

## 📱 Complete React Component (Copy & Use)

```jsx
import React, { useState, useEffect } from 'react';

const CourseRecommendations = () => {
  const [recommendations, setRecommendations] = useState([]);
  const [loading, setLoading] = useState(true);
  const token = localStorage.getItem('token'); // Your JWT token

  useEffect(() => {
    fetchRecommendations();
  }, []);

  const fetchRecommendations = async () => {
    try {
      const response = await fetch('http://127.0.0.1:8000/api/auth/recommendations?limit=5', {
        headers: { 'Authorization': `Bearer ${token}` }
      });
      const data = await response.json();
      setRecommendations(data.data.recommendations);
    } catch (error) {
      console.error('Error:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <div>Loading...</div>;

  return (
    <div className="recommendations">
      <h2>🎯 Recommended Courses for You</h2>
      <div className="course-grid">
        {recommendations.map(course => (
          <div key={course.id} className="course-card">
            {/* Relevance Badge */}
            {course.relevance_score && (
              <div className="relevance-badge">
                Score: {course.relevance_score}
              </div>
            )}
            
            {/* Course Image */}
            <img src={course.image} alt={course.title} />
            
            {/* Course Details */}
            <h3>{course.title}</h3>
            <p>{course.description}</p>
            
            {/* Category & Level */}
            <div className="badges">
              <span className="badge">{course.category}</span>
              <span className="badge">{course.level}</span>
            </div>
            
            {/* Rating */}
            <div className="rating">
              ⭐ {course.average_rating.toFixed(1)} ({course.total_reviews} reviews)
            </div>
            
            {/* Price & Enroll Button */}
            <div className="footer">
              <span className="price">
                {course.access_type === 'free' ? 'FREE' : 
                  `Rp ${course.price.toLocaleString('id-ID')}`}
              </span>
              <button onClick={() => window.location.href = `/course/${course.id}`}>
                View Details
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default CourseRecommendations;
```

---

## 🎨 Styling (CSS)

```css
.recommendations {
  padding: 20px;
}

.course-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.course-card {
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
  position: relative;
}

.course-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.relevance-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: #4CAF50;
  color: white;
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
  z-index: 10;
}

.course-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.course-card h3 {
  padding: 15px 15px 10px;
  margin: 0;
  font-size: 18px;
}

.course-card p {
  padding: 0 15px;
  color: #666;
  font-size: 14px;
  line-height: 1.5;
}

.badges {
  padding: 10px 15px;
  display: flex;
  gap: 8px;
}

.badge {
  background: #e3f2fd;
  color: #1976d2;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
}

.rating {
  padding: 10px 15px;
  color: #f57c00;
  font-weight: 600;
}

.footer {
  padding: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid #e0e0e0;
}

.price {
  font-size: 18px;
  font-weight: bold;
  color: #2e7d32;
}

.footer button {
  background: #1976d2;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.footer button:hover {
  background: #1565c0;
}

@media (max-width: 768px) {
  .course-grid {
    grid-template-columns: 1fr;
  }
}
```

---

## 📋 Profile Update Form (Copy & Use)

```jsx
import React, { useState } from 'react';

const ProfileForm = () => {
  const [major, setMajor] = useState('');
  const [specializations, setSpecializations] = useState([]);
  const [inputValue, setInputValue] = useState('');
  const token = localStorage.getItem('token');

  const addSpecialization = () => {
    if (inputValue.trim() && !specializations.includes(inputValue.trim())) {
      setSpecializations([...specializations, inputValue.trim()]);
      setInputValue('');
    }
  };

  const removeSpecialization = (spec) => {
    setSpecializations(specializations.filter(s => s !== spec));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    try {
      const response = await fetch('http://127.0.0.1:8000/api/auth/profile', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          major: major,
          specialization: specializations
        })
      });
      
      const data = await response.json();
      
      if (data.success) {
        alert('Profile updated! You will now get better recommendations.');
      }
    } catch (error) {
      console.error('Error:', error);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="profile-form">
      <h2>Update Your Profile</h2>
      
      {/* Major/Jurusan */}
      <div className="form-group">
        <label>Major / Jurusan</label>
        <input
          type="text"
          value={major}
          onChange={(e) => setMajor(e.target.value)}
          placeholder="e.g., Teknik Informatika"
          className="form-control"
        />
      </div>

      {/* Specialization/Minat */}
      <div className="form-group">
        <label>Interests / Minat</label>
        <div className="input-with-button">
          <input
            type="text"
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            onKeyPress={(e) => e.key === 'Enter' && (e.preventDefault(), addSpecialization())}
            placeholder="e.g., Web Development"
            className="form-control"
          />
          <button type="button" onClick={addSpecialization} className="btn-add">
            Add
          </button>
        </div>

        {/* Display Tags */}
        <div className="tags">
          {specializations.map((spec, index) => (
            <span key={index} className="tag">
              {spec}
              <button type="button" onClick={() => removeSpecialization(spec)}>
                ×
              </button>
            </span>
          ))}
        </div>
      </div>

      <button type="submit" className="btn-submit">
        Update Profile
      </button>
    </form>
  );
};

export default ProfileForm;
```

### Form Styling

```css
.profile-form {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #333;
}

.form-control {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
}

.input-with-button {
  display: flex;
  gap: 8px;
}

.input-with-button input {
  flex: 1;
}

.btn-add {
  padding: 12px 24px;
  background: #1976d2;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 12px;
}

.tag {
  background: #e3f2fd;
  color: #1976d2;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.tag button {
  background: none;
  border: none;
  color: #1976d2;
  font-size: 18px;
  cursor: pointer;
  padding: 0;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-submit {
  width: 100%;
  padding: 14px;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 10px;
}

.btn-submit:hover {
  background: #45a049;
}
```

---

## 🔑 Important Points

### 1. **Authentication**
Always include JWT token in headers:
```javascript
headers: {
  'Authorization': `Bearer ${yourToken}`
}
```

### 2. **Response Structure**
```javascript
{
  success: true,
  message: "...",
  data: {
    recommendations: [...],  // Array of courses
    criteria: {              // Algorithm info
      subscription_plan: "premium",
      specializations: [...],
      major: "...",
      excluded_enrolled: 3
    }
  }
}
```

### 3. **Relevance Score**
- **150+**: Exact match with specialization in title
- **100-149**: Match in category
- **80-99**: Match in description
- **60-79**: Match with major in title
- **30-59**: Match with major in category/description

### 4. **Access Types**
- `free`: Available to all users
- `regular`: Requires regular or premium subscription
- `premium`: Requires premium subscription only

---

## 🧪 Testing

### Test in Browser Console:
```javascript
// Get token from localStorage
const token = localStorage.getItem('token');

// Test update profile
fetch('http://127.0.0.1:8000/api/auth/profile', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  },
  body: JSON.stringify({
    major: "Teknik Informatika",
    specialization: ["Web Development", "React"]
  })
})
.then(r => r.json())
.then(d => console.log('✅ Profile:', d));

// Test get recommendations
fetch('http://127.0.0.1:8000/api/auth/recommendations?limit=5', {
  headers: { 'Authorization': `Bearer ${token}` }
})
.then(r => r.json())
.then(d => console.log('✅ Recommendations:', d));
```

---

## 📚 Documentation Links

- **Full Guide**: `/docs/FRONTEND_IMPLEMENTATION_GUIDE.md`
- **Algorithm Details**: `/docs/COURSE_RECOMMENDATION_SYSTEM.md`
- **Swagger UI**: `http://127.0.0.1:8000/api/documentation`
- **API Endpoints**: `http://127.0.0.1:8000/api`

---

## ⚡ Quick Tips

1. **Always update profile first** before fetching recommendations
2. **Use limit parameter** to control number of recommendations
3. **Display relevance_score** to show why course is recommended
4. **Show criteria info** to be transparent about algorithm
5. **Refresh recommendations** after profile update
6. **Handle empty states** when no recommendations available
7. **Use loading states** for better UX

---

## 🎯 User Flow

```
1. User registers/logs in
   ↓
2. User updates profile with specialization & major
   ↓
3. System generates personalized recommendations
   ↓
4. User views recommended courses with relevance scores
   ↓
5. User enrolls in recommended course
   ↓
6. System excludes enrolled course from future recommendations
```

---

## 🐛 Common Issues

**Issue**: Empty recommendations
- **Solution**: User needs to update profile with specialization/major

**Issue**: All courses have low relevance score
- **Solution**: User's interests don't match available courses

**Issue**: No free courses in recommendations
- **Solution**: User has free plan but all matching courses are premium

**Issue**: 401 Unauthorized
- **Solution**: Check if JWT token is valid and included in headers

---

**Happy Coding! 🚀**
