import { Routes, Route } from "react-router-dom";
import { AuthProvider } from "./context/AuthContext";
import MainLayout from "./layouts/MainLayout";
import LandingPage from "./pages/LandingPage";
import Login from "./pages/Login";
import Register from "./pages/Register";
import Dashboard from "./pages/Dashboard";
import CourseList from "./pages/CourseList";
import ScholarshipList from "./pages/ScholarshipList";
import ArticleList from "./pages/ArticleList";
import MentoringList from "./pages/MentoringList";
import Profile from "./pages/Profile";
import "./App.css";

function App() {
    return (
        <AuthProvider>
            <Routes>
                <Route path="/" element={<MainLayout />}>
                    <Route index element={<LandingPage />} />
                    <Route path="login" element={<Login />} />
                    <Route path="register" element={<Register />} />
                    <Route path="courses" element={<CourseList />} />
                    <Route path="scholarships" element={<ScholarshipList />} />
                    <Route path="articles" element={<ArticleList />} />

                    {/* Protected Routes (In a real app, wrap these in a PrivateRoute component) */}
                    <Route path="dashboard" element={<Dashboard />} />
                    <Route path="mentoring" element={<MentoringList />} />
                    <Route path="profile" element={<Profile />} />
                </Route>
            </Routes>
        </AuthProvider>
    );
}

export default App;
