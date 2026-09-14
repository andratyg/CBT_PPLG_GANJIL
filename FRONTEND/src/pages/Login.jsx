import React, { useState } from 'react'
import { Link } from 'react-router-dom'
import api from '../api'
import Button from '../components/Button'
import FormInput from '../components/FormInput'
import { useTheme } from '../utils/useTheme'

export default function Login({ onLogin }) {
  const { isDark, toggleTheme } = useTheme()
  const [authMode, setAuthMode] = useState('login') // 'login' | 'register'
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')

  const handleLogin = async (e) => {
    if (e) e.preventDefault()
    setLoading(true)
    setError('')
    try {
      const res = await api.post('/login', { email, password })
      const token = res.data.token
      const userData = res.data.user
      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(userData))
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`
      onLogin(userData)
    } catch (err) {
      console.error(err)
      setError(err.response?.data?.message || 'Login gagal. Periksa kembali email dan password Guru.')
    } finally {
      setLoading(false)
    }
  }

  const handleRegister = async (e) => {
    if (e) e.preventDefault()
    setLoading(true)
    setError('')
    try {
      const res = await api.post('/register', { name, email, password, role: 'guru' })
      const token = res.data.token
      const userData = res.data.user
      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(userData))
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`
      onLogin(userData)
    } catch (err) {
      console.error(err)
      setError(err.response?.data?.message || 'Registrasi gagal. Pastikan email belum digunakan.')
    } finally {
      setLoading(false)
    }
  }

  const fillGuruDemo = () => {
    setName('Bu Yayu')
    setEmail('guru@cbt.test')
    setPassword('password')
    setError('')
  }

  return (
    <div
      style={{
        minHeight: '100vh',
        background: isDark
          ? 'radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.12), transparent 40%), radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.12), transparent 40%), #070b14'
          : 'radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.15), transparent 40%), radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.1), transparent 40%), #f8fafc',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center',
        padding: '1.5rem',
        position: 'relative'
      }}
    >
      {/* Top Navbar */}
      <div
        style={{
          position: 'absolute',
          top: '1.25rem',
          right: '1.5rem',
          left: '1.5rem',
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          maxWidth: '1200px',
          margin: '0 auto',
          zIndex: 10
        }}
      >
        <Link to="/" style={{ display: 'flex', alignItems: 'center', gap: '0.6rem', textDecoration: 'none' }}>
          <span className="student-brand-badge">PPLG</span>
          <strong style={{ fontSize: '1.15rem', color: 'var(--text)', letterSpacing: '-0.02em', fontWeight: 800 }}>CBT System</strong>
        </Link>

        <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem' }}>
          <Link
            to="/"
            style={{
              display: 'inline-flex',
              alignItems: 'center',
              gap: '0.4rem',
              fontSize: '0.8rem',
              fontWeight: 600,
              color: 'var(--text)',
              textDecoration: 'none',
              padding: '0.4rem 0.85rem',
              borderRadius: 'var(--radius-full)',
              background: 'var(--card)',
              border: '1px solid var(--border)',
              boxShadow: 'var(--shadow-sm)',
              transition: 'var(--transition)'
            }}
          >
            <span>←</span>
            <span>Portal Siswa (Bebas Akses)</span>
          </Link>
          <button
            type="button"
            className="theme-toggle-btn"
            onClick={toggleTheme}
            title={isDark ? 'Beralih ke Mode Terang (Light)' : 'Beralih ke Mode Gelap (Dark)'}
            aria-label="Toggle Tema"
          >
            <span className="theme-toggle-icon">{isDark ? '☀️' : '🌙'}</span>
          </button>
        </div>
      </div>

      {/* Login Card */}
      <div
        className="card"
        style={{
          width: '100%',
          maxWidth: '440px',
          background: 'var(--card)',
          border: '1px solid var(--border)',
          borderRadius: 'var(--radius-lg)',
          boxShadow: 'var(--shadow-lg)',
          padding: '2.5rem 2rem',
          marginTop: '2rem'
        }}
      >
        <div style={{ textAlign: 'center', marginBottom: '1.5rem' }}>
          <div
            style={{
              width: '58px',
              height: '58px',
              background: 'var(--accent-light)',
              borderRadius: '16px',
              display: 'inline-flex',
              alignItems: 'center',
              justifyContent: 'center',
              fontSize: '1.85rem',
              marginBottom: '0.85rem',
              border: '1px solid var(--accent-border)'
            }}
          >
            👨‍🏫
          </div>
          <h2 style={{ fontSize: '1.55rem', fontWeight: 800, color: 'var(--text)', letterSpacing: '-0.03em' }}>
            {authMode === 'login' ? 'Portal Masuk Guru' : 'Daftar Akun Guru Baru'}
          </h2>
          <p style={{ color: 'var(--muted)', fontSize: '0.875rem', marginTop: '0.35rem', lineHeight: 1.5 }}>
            {authMode === 'login'
              ? 'Khusus Bapak/Ibu Guru untuk administrasi kelas, modul materi, absensi, dan penilaian'
              : 'Daftarkan identitas guru baru untuk mulai mengelola rombel dan KBM'}
          </p>
        </div>

        {/* Notice: Siswa Tidak Perlu Login */}
        <div
          style={{
            background: 'rgba(59, 130, 246, 0.08)',
            border: '1px solid rgba(59, 130, 246, 0.25)',
            borderRadius: 'var(--radius-md)',
            padding: '0.75rem 1rem',
            marginBottom: '1.25rem',
            fontSize: '0.825rem',
            color: 'var(--text)',
            display: 'flex',
            alignItems: 'center',
            gap: '0.65rem'
          }}
        >
          <span style={{ fontSize: '1.2rem' }}>ℹ️</span>
          <div>
            <strong>Untuk Siswa:</strong> Anda <u>tidak perlu login</u>. Silakan langsung buka{' '}
            <Link to="/" style={{ color: 'var(--accent)', fontWeight: 800, textDecoration: 'underline' }}>
              Portal Siswa Bebas Akses &rarr;
            </Link>
          </div>
        </div>

        {/* Auth Mode Tabs */}
        <div
          style={{
            display: 'grid',
            gridTemplateColumns: '1fr 1fr',
            gap: '0.35rem',
            background: 'var(--surface-alt)',
            padding: '0.3rem',
            borderRadius: 'var(--radius-md)',
            marginBottom: '1.25rem'
          }}
        >
          <button
            type="button"
            onClick={() => {
              setAuthMode('login')
              setError('')
            }}
            style={{
              padding: '0.45rem',
              borderRadius: 'var(--radius-sm)',
              border: 'none',
              background: authMode === 'login' ? 'var(--card)' : 'transparent',
              color: authMode === 'login' ? 'var(--text)' : 'var(--muted)',
              fontWeight: 700,
              fontSize: '0.825rem',
              cursor: 'pointer',
              boxShadow: authMode === 'login' ? 'var(--shadow-sm)' : 'none',
              transition: 'all 0.15s ease'
            }}
          >
            Masuk (Login)
          </button>
          <button
            type="button"
            onClick={() => {
              setAuthMode('register')
              setError('')
            }}
            style={{
              padding: '0.45rem',
              borderRadius: 'var(--radius-sm)',
              border: 'none',
              background: authMode === 'register' ? 'var(--card)' : 'transparent',
              color: authMode === 'register' ? 'var(--text)' : 'var(--muted)',
              fontWeight: 700,
              fontSize: '0.825rem',
              cursor: 'pointer',
              boxShadow: authMode === 'register' ? 'var(--shadow-sm)' : 'none',
              transition: 'all 0.15s ease'
            }}
          >
            + Daftar Akun Guru
          </button>
        </div>

        {error && (
          <div
            className="badge badge-danger mb-4"
            style={{
              width: '100%',
              padding: '0.75rem 1rem',
              borderRadius: 'var(--radius-md)',
              fontSize: '0.85rem',
              lineHeight: 1.4
            }}
          >
            ⚠️ {error}
          </div>
        )}

        {authMode === 'login' ? (
          <form onSubmit={handleLogin}>
            <FormInput
              type="email"
              label="Email Akun Guru"
              name="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="guru@cbt.test"
              required
            />

            <FormInput
              type="password"
              label="Kata Sandi"
              name="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="••••••••"
              required
            />

            <Button
              type="submit"
              variant="primary"
              size="lg"
              loading={loading}
              style={{ width: '100%', marginTop: '0.75rem' }}
            >
              Masuk sebagai Guru &rarr;
            </Button>
          </form>
        ) : (
          <form onSubmit={handleRegister}>
            <FormInput
              type="text"
              label="Nama Lengkap Guru & Gelar"
              name="name"
              value={name}
              onChange={(e) => setName(e.target.value)}
              placeholder="Contoh: Bu Yayu, S.Pd."
              required
            />

            <FormInput
              type="email"
              label="Email Akun Guru"
              name="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="guru@cbt.test"
              required
            />

            <FormInput
              type="password"
              label="Kata Sandi (Minimal 4 Karakter)"
              name="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="••••••••"
              required
            />

            <Button
              type="submit"
              variant="primary"
              size="lg"
              loading={loading}
              style={{ width: '100%', marginTop: '0.75rem' }}
            >
              Daftarkan Akun Guru & Masuk &rarr;
            </Button>
          </form>
        )}

        {/* Quick Demo Fill Button */}
        <div style={{ marginTop: '1.5rem', paddingTop: '1.25rem', borderTop: '1px solid var(--border)', textAlign: 'center' }}>
          <button
            type="button"
            className="btn btn-sm btn-secondary"
            onClick={fillGuruDemo}
            style={{ width: '100%', padding: '0.6rem' }}
            title="Isi data akun default Bu Yayu"
          >
            ⚡ Isi Data Cepat Bu Yayu (guru@cbt.test)
          </button>
        </div>

        <div style={{ textAlign: 'center', marginTop: '1.5rem' }}>
          <Link to="/" style={{ fontSize: '0.85rem', color: 'var(--accent)', fontWeight: 600, textDecoration: 'none' }}>
            &larr; Masuk ke Dashboard Siswa (Tanpa Login)
          </Link>
        </div>
      </div>
    </div>
  )
}
