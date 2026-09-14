import React from 'react'
import { useLocation, Link } from 'react-router-dom'
import { useTheme } from '../utils/useTheme'
import './Header.css'

const routeTitles = {
  '/dashboard': { section: 'Ringkasan', title: 'Dashboard Utama', icon: '📊' },
  '/kelas': { section: 'Akademik', title: 'Manajemen Kelas', icon: '👥' },
  '/mapel': { section: 'Akademik', title: 'Mata Pelajaran', icon: '📖' },
  '/jadwal': { section: 'Jadwal', title: 'Agenda Pelajaran', icon: '📅' },
  '/pertemuan': { section: 'Pembelajaran', title: 'Pertemuan & Materi', icon: '📁' },
  '/tugas': { section: 'Evaluasi', title: 'Pengumpulan Tugas', icon: '📝' },
  '/absensi': { section: 'Presensi', title: 'Kehadiran Siswa', icon: '📋' },
  '/jurnal': { section: 'Administrasi', title: 'Jurnal Mengajar', icon: '📘' }
}

export default function Header({ user, onLogout, onToggleMobileMenu }) {
  const { isDark, toggleTheme } = useTheme()
  const location = useLocation()
  
  const currentRoute = routeTitles[location.pathname] || {
    section: 'Portal Guru',
    title: 'CBT System PPLG',
    icon: '⚡'
  }

  const today = new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })

  return (
    <header className="header">
      <div className="header-left">
        <button
          type="button"
          className="menu-toggle-btn"
          onClick={onToggleMobileMenu}
          aria-label="Buka Menu"
        >
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="18" x2="21" y2="18" />
          </svg>
        </button>

        {/* Dynamic Page Breadcrumb */}
        <div className="header-breadcrumb">
          <span className="breadcrumb-section">{currentRoute.section}</span>
          <span className="breadcrumb-separator">/</span>
          <div className="breadcrumb-current">
            <span className="breadcrumb-icon">{currentRoute.icon}</span>
            <span className="breadcrumb-title">{currentRoute.title}</span>
          </div>
        </div>
      </div>

      <div className="header-right">
        {/* Date Display */}
        <div className="header-date-badge">
          <svg className="header-date-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
          </svg>
          <span>{today}</span>
        </div>

        {/* System Online Pill */}
        <div className="system-pill" title="Koneksi API Backend Aktif">
          <span className="online-dot" />
          <span className="system-text">Sistem Online</span>
        </div>

        {/* View Student Portal Link */}
        <Link
          to="/siswa"
          className="student-portal-link"
          title="Buka tampilan Portal Siswa (Mode Pratinjau Guru)"
        >
          <span className="portal-icon">🎓</span>
          <span className="portal-text">Portal Siswa</span>
          <svg className="portal-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
            <path d="M7 17L17 7M17 7H7M17 7V17" />
          </svg>
        </Link>

        {/* Theme Toggle Button */}
        <button
          type="button"
          className="theme-toggle-btn"
          onClick={toggleTheme}
          title={isDark ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'}
          aria-label="Toggle Mode Gelap/Terang"
        >
          <span className="theme-toggle-icon">{isDark ? '☀️' : '🌙'}</span>
        </button>

        <div className="header-divider" />

        {/* User Profile Pill */}
        <div className="header-user-pill" title={`Masuk sebagai ${user?.name || 'Guru'}`}>
          <div className="header-user-avatar-wrap">
            <div className="header-user-avatar">
              {user?.name ? user.name.charAt(0).toUpperCase() : 'G'}
            </div>
            <span className="avatar-status-dot" />
          </div>
          <div className="header-user-text">
            <span className="header-name">{user?.name || 'Bu Yayu'}</span>
            <span className="header-role">Pendidik • Bahasa Indonesia</span>
          </div>
        </div>

        {/* Logout Button */}
        <button
          type="button"
          onClick={onLogout}
          className="header-logout-btn"
          title="Keluar dari sesi Guru"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
          <span className="logout-text">Keluar</span>
        </button>
      </div>
    </header>
  )
}
