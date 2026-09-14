import React, { useState } from 'react'
import { BrowserRouter, Routes, Route, Navigate, Outlet } from 'react-router-dom'
import api from './api'
import Sidebar from './components/Sidebar'
import Header from './components/Header'
import Login from './pages/Login'
import StudentDashboard from './pages/StudentDashboard'
import Dashboard from './pages/Dashboard'
import KelasPage from './pages/KelasPage'
import MapelPage from './pages/MapelPage'
import JadwalPage from './pages/JadwalPage'
import PertemuanMateriPage from './pages/PertemuanMateriPage'
import TugasPage from './pages/TugasPage'
import AbsensiPage from './pages/AbsensiPage'
import JurnalPage from './pages/JurnalPage'

function PrivateAppLayout({ user, onLogout }) {
  const [mobileOpen, setMobileOpen] = useState(false)

  return (
    <div className="app-shell">
      <Sidebar
        user={user}
        mobileOpen={mobileOpen}
        onCloseMobile={() => setMobileOpen(false)}
      />
      <div className="content-wrap">
        <Header
          user={user}
          onLogout={onLogout}
          onToggleMobileMenu={() => setMobileOpen((prev) => !prev)}
        />
        <main className="page-body">
          <Outlet />
        </main>
      </div>
    </div>
  )
}

function App() {
  const [user, setUser] = useState(() => {
    try {
      const token = localStorage.getItem('token')
      const saved = localStorage.getItem('user')
      if (!token || !saved) {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        return null
      }
      return JSON.parse(saved)
    } catch {
      return null
    }
  })

  const handleLogout = async () => {
    try {
      await api.post('/logout')
    } catch (err) {
      console.warn('Logout error:', err)
    }
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    setUser(null)
  }

  const isGuru = user && user.role === 'guru'

  return (
    <BrowserRouter>
      <Routes>
        {/* Rute Portal Siswa - Terbuka untuk umum & Guru dapat melihat pratinjau tanpa logout */}
        <Route
          path="/siswa"
          element={<StudentDashboard isTeacherViewing={isGuru} teacherUser={user} />}
        />
        <Route
          path="/siswa/*"
          element={<StudentDashboard isTeacherViewing={isGuru} teacherUser={user} />}
        />
        <Route
          path="/student"
          element={<StudentDashboard isTeacherViewing={isGuru} teacherUser={user} />}
        />
        <Route
          path="/student/*"
          element={<StudentDashboard isTeacherViewing={isGuru} teacherUser={user} />}
        />
        <Route
          path="/portal-siswa"
          element={<StudentDashboard isTeacherViewing={isGuru} teacherUser={user} />}
        />

        {/* Jika User adalah Guru (Bu Yayu), sediakan layout dashboard guru */}
        {isGuru ? (
          <Route element={<PrivateAppLayout user={user} onLogout={handleLogout} />}>
            <Route path="/dashboard" element={<Dashboard user={user} />} />
            <Route path="/kelas" element={<KelasPage user={user} />} />
            <Route path="/mapel" element={<MapelPage user={user} />} />
            <Route path="/jadwal" element={<JadwalPage user={user} />} />
            <Route path="/pertemuan" element={<PertemuanMateriPage user={user} />} />
            <Route path="/tugas" element={<TugasPage user={user} />} />
            <Route path="/absensi" element={<AbsensiPage user={user} />} />
            <Route path="/jurnal" element={<JurnalPage user={user} />} />
            <Route path="/" element={<Navigate to="/dashboard" replace />} />
            <Route path="/login" element={<Navigate to="/dashboard" replace />} />
            <Route path="*" element={<Navigate to="/dashboard" replace />} />
          </Route>
        ) : (
          <>
            {/* Rute Siswa & Akses Publik (Bebas Akses Tanpa Perlu Login) */}
            <Route path="/" element={<StudentDashboard />} />
            <Route path="/jadwal" element={<StudentDashboard initialTab="jadwal" />} />
            <Route path="/absensi" element={<StudentDashboard initialTab="absensi" />} />
            <Route path="/materi" element={<StudentDashboard initialTab="materi" />} />
            <Route path="/tugas" element={<StudentDashboard initialTab="tugas" />} />

            {/* Halaman Login Khusus Guru (Bu Yayu) */}
            <Route path="/login" element={<Login onLogin={setUser} />} />

            {/* Rute lainnya bagi non-guru diarahkan ke Portal Siswa */}
            <Route path="*" element={<Navigate to="/" replace />} />
          </>
        )}
      </Routes>
    </BrowserRouter>
  )
}

export default App
