import React from 'react'
import { NavLink } from 'react-router-dom'
import './Sidebar.css'

const navGroups = [
  {
    groupTitle: 'UTAMA',
    items: [
      {
        to: '/dashboard',
        label: 'Dashboard',
        badge: 'Live',
        icon: (
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <rect x="3" y="3" width="7" height="7" rx="1.5" />
            <rect x="14" y="3" width="7" height="7" rx="1.5" />
            <rect x="14" y="14" width="7" height="7" rx="1.5" />
            <rect x="3" y="14" width="7" height="7" rx="1.5" />
          </svg>
        )
      },
      {
        to: '/siswa',
        label: 'Portal Siswa',
        badge: 'Pratinjau',
        icon: (
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
            <path d="M6 12v5c3 3 9 3 12 0v-5" />
          </svg>
        )
      }
    ]
  },
  {
    groupTitle: 'AKADEMIK',
    items: [
      {
        to: '/kelas',
        label: 'Data Kelas',
        icon: (
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
          </svg>
        )
      },
      {
        to: '/mapel',
        label: 'Mata Pelajaran',
        icon: (
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
          </svg>
        )
      },
      {
        to: '/jadwal',
        label: 'Jadwal Pelajaran',
        icon: (
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
          </svg>
        )
      }
    ]
  },
  {
    groupTitle: 'PEMBELAJARAN',
    items: [
      {
        to: '/pertemuan',
        label: 'Pertemuan & Materi',
        icon: (
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <polygon points="12 2 2 7 12 12 22 7 12 2" />
            <polyline points="2 17 12 22 22 17" />
            <polyline points="2 12 12 17 22 12" />
          </svg>
        )
      },
      {
        to: '/tugas',
        label: 'Tugas Siswa',
        icon: (
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M9 11l3 3L22 4" />
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
          </svg>
        )
      }
    ]
  },
  {
    groupTitle: 'EVALUASI & PRESENSI',
    items: [
      {
        to: '/absensi',
        label: 'Presensi Siswa',
        badge: 'Dispen',
        icon: (
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <polyline points="16 11 18 13 22 9" />
          </svg>
        )
      },
      {
        to: '/jurnal',
        label: 'Jurnal Mengajar',
        icon: (
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
          </svg>
        )
      }
    ]
  }
]

export default function Sidebar({ user, mobileOpen, onCloseMobile }) {
  return (
    <>
      {mobileOpen && (
        <div className="sidebar-backdrop" onClick={onCloseMobile} />
      )}
      <aside className={`sidebar ${mobileOpen ? 'mobile-open' : ''}`}>
        {/* Brand Logo & Title */}
        <div className="sidebar-brand">
          <div className="sidebar-logo">
            <div className="logo-badge-icon">
              <span className="logo-spark">✦</span>
              <span className="logo-text">PPLG</span>
            </div>
            <div className="brand-text">
              <div className="brand-title-row">
                <h2>CBT System</h2>
                <span className="brand-badge-pro">PRO</span>
              </div>
              <span className="brand-sub">Bahasa Indonesia • Semua Jurusan</span>
            </div>
          </div>
          {onCloseMobile && (
            <button
              className="sidebar-close-mobile"
              onClick={onCloseMobile}
              aria-label="Tutup menu"
            >
              &times;
            </button>
          )}
        </div>

        {/* Grouped Nav Items */}
        <div className="sidebar-nav-wrap">
          {navGroups.map((group) => (
            <div key={group.groupTitle} className="nav-group-section">
              <div className="nav-group-title">{group.groupTitle}</div>
              <nav className="sidebar-nav">
                {group.items.map((item) => (
                  <NavLink
                    key={item.to}
                    to={item.to}
                    className={({ isActive }) =>
                      `nav-link ${isActive ? 'active' : ''}`
                    }
                    onClick={onCloseMobile}
                  >
                    <span className="nav-icon">{item.icon}</span>
                    <span className="nav-text">{item.label}</span>
                    {item.badge && (
                      <span className={`nav-badge ${item.badge === 'Live' ? 'badge-live' : 'badge-soft'}`}>
                        {item.badge}
                      </span>
                    )}
                  </NavLink>
                ))}
              </nav>
            </div>
          ))}
        </div>

        {/* User Card in Footer */}
        <div className="sidebar-footer">
          <div className="user-profile-badge">
            <div className="user-avatar-wrap">
              <div className="user-avatar">
                {user?.name ? user.name.charAt(0).toUpperCase() : 'U'}
              </div>
              <span className="user-online-indicator" />
            </div>
            <div className="user-meta">
              <span className="user-name">{user?.name || 'Bu Yayu'}</span>
              <span className="user-role-text">
                {user?.role === 'guru' ? 'Guru Bahasa Indonesia' : (user?.role || 'Guru')}
              </span>
            </div>
          </div>
        </div>
      </aside>
    </>
  )
}
