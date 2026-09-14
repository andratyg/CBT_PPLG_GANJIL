import React, { useState, useEffect, useMemo, useCallback } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import api from '../api'
import Card from '../components/Card'
import Button from '../components/Button'
import Modal from '../components/Modal'
import FormInput from '../components/FormInput'
import { exportToExcel, exportToCSV, printAttendanceReport, exportMatrixToExcel } from '../utils/exportAttendance'
import { useTheme } from '../utils/useTheme'

const STORAGE_URL = import.meta.env.VITE_STORAGE_URL || 'http://localhost:8000/storage/'

export default function StudentDashboard({ initialTab: propInitialTab, isTeacherViewing, teacherUser }) {
  const { isDark, toggleTheme } = useTheme()
  const [searchParams, setSearchParams] = useSearchParams()
  const [activeTabState, setActiveTabState] = useState(() => propInitialTab || searchParams.get('tab') || 'overview')
  const activeTab = propInitialTab || searchParams.get('tab') || activeTabState

  const switchTab = useCallback((tab) => {
    setActiveTabState(tab)
    setSearchParams({ tab })
  }, [setSearchParams])

  const isTeacher = Boolean(
    isTeacherViewing ||
    (() => {
      try {
        const u = localStorage.getItem('user')
        return u && JSON.parse(u)?.role === 'guru'
      } catch {
        return false
      }
    })()
  )

  const currentTeacher = teacherUser || (() => {
    try {
      return isTeacher ? JSON.parse(localStorage.getItem('user') || '{}') : null
    } catch {
      return null
    }
  })()

  const [stats, setStats] = useState(null)
  const [jadwalList, setJadwalList] = useState([])
  const [pertemuanList, setPertemuanList] = useState([])
  const [kelasList, setKelasList] = useState([])
  const [selectedPertemuan, setSelectedPertemuan] = useState(null)
  const [materiList, setMateriList] = useState([])
  const [tugasList, setTugasList] = useState([])

  const [loading, setLoading] = useState(true)
  const [loadingMateri, setLoadingMateri] = useState(false)
  const [selectedDay, setSelectedDay] = useState('semua')

  // Absensi & Jurnal data
  const [absensiList, setAbsensiList] = useState([])
  const [jurnalPertemuan, setJurnalPertemuan] = useState(null)
  const [loadingAbsensi, setLoadingAbsensi] = useState(false)

  // Absensi Multi-Pertemuan & Riwayat Siswa
  const [matrixData, setMatrixData] = useState(null)
  const [selectedKelasId, setSelectedKelasId] = useState('1')
  const [selectedSiswaId, setSelectedSiswaId] = useState(() => localStorage.getItem('cbt_student_id') || '')
  const [studentHistoryFilter, setStudentHistoryFilter] = useState('semua')
  const [presensiSubTab, setPresensiSubTab] = useState('personal') // 'personal' | 'matriks' | 'sesi'

  const handleStudentSelectKelas = async (kId) => {
    setSelectedKelasId(kId)
    try {
      const res = await api.get('/absensi/matrix', { params: { kelas_id: kId } })
      setMatrixData(res.data)
      const siswas = res.data?.siswa || []
      if (siswas.length > 0) {
        setSelectedSiswaId(String(siswas[0].id))
        localStorage.setItem('cbt_student_id', String(siswas[0].id))
      }
    } catch (err) {
      console.error(err)
    }
  }

  // Modal Kumpulkan Tugas Siswa
  const [submitModalTugas, setSubmitModalTugas] = useState(null)
  const [submitForm, setSubmitForm] = useState({
    siswa_id: '',
    catatan: '',
    file: null
  })
  const [submitting, setSubmitting] = useState(false)
  const [submitError, setSubmitError] = useState('')

  const viewPertemuan = async (p) => {
    setSelectedPertemuan(p)
    setLoadingMateri(true)
    setLoadingAbsensi(true)
    try {
      const [matRes, tugRes, absRes, jurRes] = await Promise.allSettled([
        api.get(`/pertemuan/${p.id}/materi`),
        api.get(`/pertemuan/${p.id}/tugas`),
        api.get(`/pertemuan/${p.id}/absensi`),
        api.get(`/pertemuan/${p.id}/jurnal`)
      ])
      if (matRes.status === 'fulfilled') setMateriList(matRes.value.data || [])
      if (tugRes.status === 'fulfilled') setTugasList(tugRes.value.data || [])
      if (absRes.status === 'fulfilled') setAbsensiList(absRes.value.data || [])
      if (jurRes.status === 'fulfilled') setJurnalPertemuan(jurRes.value.data || null)
    } catch (err) {
      console.error(err)
      setMateriList(p.materi || [])
      setTugasList(p.tugas || [])
    } finally {
      setLoadingMateri(false)
      setLoadingAbsensi(false)
    }
  }

  useEffect(() => {
    Promise.allSettled([
      api.get('/public-dashboard'),
      api.get('/jadwal'),
      api.get('/pertemuan'),
      api.get('/kelas'),
      api.get('/absensi/matrix')
    ])
      .then(([statRes, jadRes, perRes, kelRes, matRes]) => {
        if (statRes.status === 'fulfilled') setStats(statRes.value.data)
        if (jadRes.status === 'fulfilled') setJadwalList(jadRes.value.data || [])
        if (perRes.status === 'fulfilled') {
          const list = perRes.value.data || []
          setPertemuanList(list)
          if (list.length > 0) {
            viewPertemuan(list[0])
          }
        }
        if (kelRes.status === 'fulfilled') setKelasList(kelRes.value.data || [])
        if (matRes.status === 'fulfilled') {
          setMatrixData(matRes.value.data)
          const siswas = matRes.value.data?.siswa || []
          const savedSId = localStorage.getItem('cbt_student_id')
          const found = siswas.find((s) => String(s.id) === String(savedSId))
          if (found) {
            setSelectedSiswaId(String(found.id))
          } else if (siswas.length > 0) {
            setSelectedSiswaId(String(siswas[0].id))
            localStorage.setItem('cbt_student_id', String(siswas[0].id))
          }
        }
      })
      .catch(console.error)
      .finally(() => setLoading(false))
  }, [])

  const handleStudentSubmit = async (e) => {
    e.preventDefault()
    if (!submitModalTugas || !submitForm.file) return
    const studentIdToUse = submitForm.siswa_id || selectedSiswaId
    if (!studentIdToUse) {
      setSubmitError('Silakan pilih nama siswa pengumpul terlebih dahulu.')
      return
    }

    setSubmitting(true)
    setSubmitError('')
    try {
      const formData = new FormData()
      formData.append('file', submitForm.file)
      formData.append('siswa_id', studentIdToUse)
      if (submitForm.catatan) formData.append('catatan', submitForm.catatan)

      await api.post(`/tugas/${submitModalTugas.id}/kumpulkan`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })

      alert('Tugas berhasil dikumpulkan!')
      setSubmitModalTugas(null)
      setSubmitForm({ siswa_id: '', catatan: '', file: null })
      if (selectedPertemuan) viewPertemuan(selectedPertemuan)
    } catch (err) {
      console.error(err)
      setSubmitError(err.response?.data?.message || 'Gagal mengirim tugas. Silakan periksa kembali file lampiran.')
    } finally {
      setSubmitting(false)
    }
  }

  const filteredJadwal = useMemo(() => {
    if (selectedDay === 'semua') return jadwalList
    return jadwalList.filter((j) => j.hari?.toLowerCase() === selectedDay.toLowerCase())
  }, [selectedDay, jadwalList])

  const currentRekap = useMemo(() => {
    if (!matrixData?.rekap_siswa || matrixData.rekap_siswa.length === 0) return null
    return (
      matrixData.rekap_siswa.find((r) => String(r.siswa?.id) === String(selectedSiswaId)) ||
      matrixData.rekap_siswa[0] ||
      null
    )
  }, [matrixData, selectedSiswaId])

  const allDetails = useMemo(() => currentRekap?.detail_pertemuan || [], [currentRekap])

  const filteredDetails = useMemo(() => {
    if (!currentRekap?.detail_pertemuan) return []
    return currentRekap.detail_pertemuan.filter((dp) => {
      if (studentHistoryFilter === 'hadir') return dp.status === 'hadir'
      if (studentHistoryFilter === 'dispen') return dp.status === 'dispen'
      if (studentHistoryFilter === 'izin_sakit') return dp.status === 'izin' || dp.status === 'sakit'
      if (studentHistoryFilter === 'alpa') return dp.status === 'alpa'
      return true
    })
  }, [currentRekap, studentHistoryFilter])

  const currentStats = useMemo(() => currentRekap?.stats || {}, [currentRekap])
  const isEligibleExam = (currentStats.persentase || 0) >= 75
  const isMemenuhi = isEligibleExam

  return (
    <div style={{ minHeight: '100vh', background: 'var(--bg)', color: 'var(--text)', display: 'flex', flexDirection: 'column', transition: 'var(--transition)' }}>
      {/* Teacher Preview Notification Banner */}
      {isTeacher && (
        <div className="teacher-preview-banner">
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.65rem' }}>
            <span style={{ fontSize: '1.25rem' }}>👨‍🏫</span>
            <div>
              <strong style={{ color: '#ffffff', fontSize: '0.875rem' }}>
                Mode Pratinjau Guru: {currentTeacher?.name || 'Bu Yayu'}
              </strong>
              <span style={{ color: '#94a3b8', marginLeft: '0.5rem', fontSize: '0.8rem' }}>
                • Anda dapat melihat seluruh modul dan presensi dari sudut pandang siswa tanpa perlu logout
              </span>
            </div>
          </div>
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
            <Link to="/dashboard" className="teacher-return-btn" title="Kembali ke Dashboard Utama Guru">
              <span>←</span>
              <span>Kembali ke Dashboard Guru</span>
            </Link>
          </div>
        </div>
      )}

      {/* Top Navigation Bar */}
      <header className="student-header">
        <div className="student-brand-wrap">
          <button
            type="button"
            className="student-brand-logo"
            onClick={() => switchTab('overview')}
            title="Kembali ke Beranda Siswa"
          >
            <span className="student-brand-badge">PPLG</span>
            <span style={{ fontWeight: 800 }}>CBT PPLG</span>
          </button>
          <div className="student-mode-badge">
            <span className="student-mode-dot" />
            <span>Mode Siswa (Akses Terbuka)</span>
          </div>
        </div>

        {/* Tab Navigation (Segmented Control) */}
        <nav className="student-nav-pills" aria-label="Navigasi Menu Siswa">
          {[
            { key: 'overview', label: 'Beranda', icon: '🏠' },
            { key: 'jadwal', label: 'Jadwal', icon: '📅' },
            { key: 'materi', label: 'Materi', icon: '📁' },
            { key: 'tugas', label: 'Tugas', icon: '📝' },
            { key: 'absensi', label: 'Presensi', icon: '📋' }
          ].map((tab) => (
            <button
              key={tab.key}
              type="button"
              className={`student-pill-btn ${activeTab === tab.key ? 'active' : ''}`}
              onClick={() => switchTab(tab.key)}
            >
              <span>{tab.icon}</span>
              <span>{tab.label}</span>
            </button>
          ))}
        </nav>

        {/* Switch to Teacher Login & Theme Toggle */}
        <div className="student-header-actions">
          <button
            type="button"
            className="theme-toggle-btn"
            onClick={toggleTheme}
            title={isDark ? 'Beralih ke Mode Terang (Light)' : 'Beralih ke Mode Gelap (Dark)'}
            aria-label="Toggle Tema"
          >
            <span className="theme-toggle-icon">{isDark ? '☀️' : '🌙'}</span>
          </button>
          {isTeacher ? (
            <Link to="/dashboard" className="teacher-return-btn" title="Kembali ke Dashboard Guru Bu Yayu">
              <span>👨‍🏫</span>
              <span>Dashboard Guru</span>
            </Link>
          ) : (
            <Link to="/login" className="teacher-login-link-btn" title="Masuk ke Portal Khusus Guru">
              <span>👨‍🏫</span>
              <span>Login Guru</span>
            </Link>
          )}
        </div>
      </header>

      {/* Main Content Area */}
      <main className="student-main-content">
        {/* TAB 1: OVERVIEW */}
        {activeTab === 'overview' && (
          <div>
            {/* Hero Card */}
            <div
              className="card mb-6"
              style={{
                background: 'radial-gradient(circle at 85% 15%, rgba(16, 185, 129, 0.22), transparent 45%), radial-gradient(circle at 15% 85%, rgba(59, 130, 246, 0.18), transparent 45%), linear-gradient(135deg, #090e1a 0%, #0f172a 100%)',
                color: '#ffffff',
                padding: 'clamp(1.5rem, 4vw, 2.75rem) clamp(1.25rem, 3vw, 2.25rem)',
                borderRadius: 'var(--radius-lg)',
                border: '1px solid rgba(255, 255, 255, 0.12)',
                position: 'relative',
                overflow: 'hidden',
                boxShadow: '0 20px 35px -10px rgba(0, 0, 0, 0.45)'
              }}
            >
              <div style={{ position: 'relative', zIndex: 2, maxWidth: '680px' }}>
                <div style={{ display: 'inline-flex', alignItems: 'center', gap: '0.5rem', marginBottom: '0.85rem' }}>
                  <span className="badge badge-accent" style={{ background: 'rgba(16, 185, 129, 0.22)', color: '#6ee7b7', border: '1px solid rgba(16, 185, 129, 0.4)' }}>
                    ✨ BAHASA INDONESIA KEJURUAN
                  </span>
                  <span style={{ fontSize: '0.75rem', color: '#94a3b8', fontWeight: 600 }}>• PPLG, TKJ, DKV, MPLB</span>
                </div>
                <h1 style={{ fontSize: 'clamp(1.5rem, 3.2vw, 2.3rem)', fontWeight: 900, lineHeight: 1.18, marginBottom: '0.85rem', letterSpacing: '-0.03em' }}>
                  Literasi & Kemahiran Berbahasa Indonesia Kejuruan
                </h1>
                <p style={{ color: '#cbd5e1', fontSize: 'clamp(0.875rem, 1.5vw, 0.975rem)', lineHeight: 1.65, marginBottom: '1.75rem' }}>
                  Akses materi ajar teks laporan hasil observasi, etika komunikasi bisnis, teks negosiasi proyek, tugas mandiri, dan rekap presensi seluruh rombel bersama Bu Yayu.
                </p>
                <div style={{ display: 'flex', gap: '0.75rem', flexWrap: 'wrap' }}>
                  <Button variant="primary" size="md" onClick={() => switchTab('materi')} icon="📚">
                    Buka Materi Pelajaran
                  </Button>
                  <Button variant="secondary" size="md" onClick={() => switchTab('jadwal')} icon="🗓️">
                    Lihat Jadwal Kelas
                  </Button>
                  <Button
                    variant="outline"
                    size="md"
                    onClick={() => switchTab('absensi')}
                    icon="📋"
                    style={{ borderColor: 'rgba(255,255,255,0.35)', color: '#ffffff' }}
                  >
                    Cek Presensi Sesi
                  </Button>
                </div>
              </div>
            </div>

            {/* Database Persistence Banner */}
            <div
              style={{
                background: 'var(--card-subtle)',
                border: '1px solid var(--border)',
                borderLeft: '4px solid var(--accent)',
                borderRadius: 'var(--radius-md)',
                padding: '1rem 1.25rem',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                flexWrap: 'wrap',
                gap: '0.75rem',
                marginBottom: '1.5rem',
                boxShadow: 'var(--shadow-sm)'
              }}
            >
              <div style={{ display: 'flex', alignItems: 'center', gap: '0.85rem' }}>
                <span style={{ fontSize: '1.5rem' }}>💾</span>
                <div>
                  <strong style={{ color: 'var(--text)', fontSize: '0.9rem' }}>Data Pembelajaran Tersimpan & Dapat Dilihat Kapan Saja</strong>
                  <div style={{ color: 'var(--text-light)', fontSize: '0.8rem', marginTop: '0.1rem' }}>
                    Semua jadwal, modul materi, tugas siswa, rekap presensi kehadiran, dan jurnal ajar tersimpan di database MySQL.
                  </div>
                </div>
              </div>
              <span className="badge badge-accent" style={{ fontSize: '0.75rem', fontWeight: 700 }}>
                🟢 Real-time Database
              </span>
            </div>

            {/* Live Stats */}
            <div className="grid grid-3 mb-6">
              <div className="stat-card">
                <div className="stat-icon green">🏫</div>
                <div>
                  <div className="stat-number">{loading ? '…' : (stats?.total_kelas ?? kelasList.length)}</div>
                  <div className="stat-label">Rombel Kelas Aktif</div>
                </div>
              </div>

              <div className="stat-card">
                <div className="stat-icon blue">📚</div>
                <div>
                  <div className="stat-number">{loading ? '…' : (stats?.total_mapel ?? '6+')}</div>
                  <div className="stat-label">Mata Pelajaran Kejuruan</div>
                </div>
              </div>

              <div className="stat-card">
                <div className="stat-icon purple">📑</div>
                <div>
                  <div className="stat-number">{loading ? '…' : (stats?.total_pertemuan ?? pertemuanList.length)}</div>
                  <div className="stat-label">Sesi Pertemuan Materi</div>
                </div>
              </div>
            </div>

            {/* Quick Sections: Jadwal Hari Ini & Materi Terbaru */}
            <div className="grid grid-2">
              <Card
                title="Jadwal Pelajaran Terdekat"
                subtitle="Agenda sesi mengajar minggu ini"
                action={
                  <button
                    type="button"
                    onClick={() => switchTab('jadwal')}
                    style={{ background: 'none', border: 'none', color: 'var(--accent)', fontWeight: 600, cursor: 'pointer', fontSize: '0.825rem' }}
                  >
                    Selengkapnya &rarr;
                  </button>
                }
              >
                {jadwalList.length === 0 ? (
                  <div className="empty-state" style={{ padding: '1.5rem' }}>
                    <div className="empty-state-icon">📅</div>
                    <div className="empty-state-title">Belum ada jadwal</div>
                  </div>
                ) : (
                  <div style={{ display: 'flex', flexDirection: 'column', gap: '0.65rem' }}>
                    {jadwalList.slice(0, 4).map((j) => (
                      <div
                        key={j.id}
                        style={{
                          display: 'flex',
                          alignItems: 'center',
                          justifyContent: 'space-between',
                          padding: '0.75rem 0.9rem',
                          background: 'var(--card-subtle)',
                          borderRadius: 'var(--radius-md)',
                          border: '1px solid var(--border)'
                        }}
                      >
                        <div>
                          <span className="badge badge-accent" style={{ textTransform: 'capitalize', fontSize: '0.7rem' }}>
                            {j.hari}
                          </span>
                          <strong style={{ display: 'block', fontSize: '0.9rem', marginTop: '0.2rem' }}>
                            {j.mapel?.nama || 'Mata Pelajaran'}
                          </strong>
                          <small className="text-muted">Kelas: {j.kelas?.nama || '–'} • Guru: {j.guru?.name || '–'}</small>
                        </div>
                        <div style={{ fontWeight: 700, fontSize: '0.85rem', color: 'var(--text-light)' }}>
                          {j.jam_mulai?.substring(0, 5)} – {j.jam_selesai?.substring(0, 5)}
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </Card>

              <Card
                title="Materi Ajar Terbaru"
                subtitle="Modul & bahan praktikum yang dapat diunduh"
                action={
                  <button
                    type="button"
                    onClick={() => switchTab('materi')}
                    style={{ background: 'none', border: 'none', color: 'var(--accent)', fontWeight: 600, cursor: 'pointer', fontSize: '0.825rem' }}
                  >
                    Buka Semua &rarr;
                  </button>
                }
              >
                {pertemuanList.length === 0 ? (
                  <div className="empty-state" style={{ padding: '1.5rem' }}>
                    <div className="empty-state-icon">📄</div>
                    <div className="empty-state-title">Belum ada materi pembelajaran</div>
                  </div>
                ) : (
                  <div style={{ display: 'flex', flexDirection: 'column', gap: '0.65rem' }}>
                    {pertemuanList.slice(0, 4).map((p) => (
                      <div
                        key={p.id}
                        onClick={() => {
                          viewPertemuan(p)
                          switchTab('materi')
                        }}
                        style={{
                          display: 'flex',
                          alignItems: 'center',
                          justifyContent: 'space-between',
                          padding: '0.75rem 0.9rem',
                          background: 'var(--card-subtle)',
                          borderRadius: 'var(--radius-md)',
                          border: '1px solid var(--border)',
                          cursor: 'pointer'
                        }}
                      >
                        <div>
                          <span className="badge badge-info" style={{ fontSize: '0.7rem' }}>
                            Pertemuan {p.pertemuan_ke}
                          </span>
                          <div style={{ fontWeight: 700, fontSize: '0.875rem', marginTop: '0.2rem' }}>
                            {p.topik}
                          </div>
                          <small className="text-muted">{p.jadwal?.mapel?.nama || 'Mapel PPLG'} • 📅 {p.tanggal}</small>
                        </div>
                        <span style={{ color: 'var(--accent)', fontWeight: 600, fontSize: '0.8rem' }}>
                          Buka Materi &rarr;
                        </span>
                      </div>
                    ))}
                  </div>
                )}
              </Card>
            </div>
          </div>
        )}

        {/* TAB 2: JADWAL */}
        {activeTab === 'jadwal' && (
          <div>
            <div className="page-header">
              <div>
                <h1 className="page-title">Jadwal Pelajaran & Mengajar</h1>
                <p className="page-subtitle">Agenda pembelajaran mingguan seluruh kelas dan program keahlian</p>
              </div>
            </div>

            {/* Day Filter */}
            <div style={{ display: 'flex', gap: '0.5rem', marginBottom: '1.5rem', overflowX: 'auto', paddingBottom: '0.35rem' }}>
              <button
                type="button"
                className={`btn btn-sm ${selectedDay === 'semua' ? 'btn-primary' : 'btn-secondary'}`}
                onClick={() => setSelectedDay('semua')}
              >
                Semua Hari
              </button>
              {['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'].map((d) => {
                const isToday = d === new Date().toLocaleDateString('id-ID', { weekday: 'long' }).toLowerCase()
                return (
                  <button
                    key={d}
                    type="button"
                    className={`btn btn-sm ${selectedDay === d ? 'btn-primary' : 'btn-secondary'}`}
                    onClick={() => setSelectedDay(d)}
                    style={{ textTransform: 'capitalize', display: 'inline-flex', alignItems: 'center', gap: '0.35rem' }}
                  >
                    <span>{d}</span>
                    {isToday && (
                      <span style={{ fontSize: '0.65rem', background: selectedDay === d ? '#ffffff' : 'var(--accent)', color: selectedDay === d ? '#000000' : '#ffffff', padding: '0.1rem 0.35rem', borderRadius: 'var(--radius-full)', fontWeight: 800 }}>
                        Hari Ini
                      </span>
                    )}
                  </button>
                )
              })}
            </div>

            <div className="grid grid-3">
              {filteredJadwal.length === 0 ? (
                <div className="card" style={{ gridColumn: '1 / -1', textAlign: 'center', padding: '3rem' }}>
                  <div className="empty-state-icon">🗓️</div>
                  <div className="empty-state-title">Tidak ada jadwal pada hari {selectedDay !== 'semua' ? selectedDay : 'ini'}</div>
                </div>
              ) : (
                filteredJadwal.map((j) => (
                  <div key={j.id} className="card card-hover">
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '0.75rem' }}>
                      <div style={{ display: 'flex', alignItems: 'center', gap: '0.4rem' }}>
                        <span className="badge badge-accent" style={{ textTransform: 'capitalize' }}>
                          {j.hari}
                        </span>
                        {j.hari?.toLowerCase() === new Date().toLocaleDateString('id-ID', { weekday: 'long' }).toLowerCase() && (
                          <span className="badge" style={{ background: '#047857', color: '#ffffff', fontSize: '0.7rem' }}>
                            🌟 Hari Ini
                          </span>
                        )}
                      </div>
                      <span style={{ fontSize: '0.825rem', fontWeight: 700, color: 'var(--muted)' }}>
                        ⏰ {j.jam_mulai?.substring(0, 5)} – {j.jam_selesai?.substring(0, 5)}
                      </span>
                    </div>

                    <h3 style={{ fontSize: '1.15rem', fontWeight: 700, marginBottom: '0.35rem' }}>
                      {j.mapel?.nama || 'Mata Pelajaran'}
                    </h3>

                    <div style={{ display: 'flex', gap: '0.4rem', marginBottom: '0.75rem' }}>
                      {(() => {
                        const upper = (j.kelas?.nama || '').toUpperCase()
                        const code = upper.includes('PPLG') ? 'PPLG' : (upper.includes('TKJ') ? 'TKJ' : (upper.includes('DKV') ? 'DKV' : (upper.includes('MPLB') ? 'MPLB' : 'SMK')))
                        return <span className={`badge badge-${code.toLowerCase()}`}>{code}</span>
                      })()}
                      <span className="badge badge-info">Kelas {j.kelas?.nama}</span>
                    </div>

                    <div className="text-muted" style={{ fontSize: '0.825rem' }}>
                      👨‍🏫 Guru Pengampu: <strong>{j.guru?.name || 'Guru'}</strong>
                    </div>
                  </div>
                ))
              )}
            </div>
          </div>
        )}

        {/* TAB 3: MATERI */}
        {activeTab === 'materi' && (
          <div>
            <div className="page-header">
              <div>
                <h1 className="page-title">Materi & Modul Pembelajaran</h1>
                <p className="page-subtitle">Unduh bahan ajar, modul praktikum, dan materi presentasi tiap pertemuan</p>
              </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: '1.5rem', alignItems: 'flex-start' }}>
              {/* Left Column: Sesi Pertemuan */}
              <Card title="Pilih Sesi Pertemuan">
                {pertemuanList.length === 0 ? (
                  <p className="text-muted" style={{ fontSize: '0.85rem' }}>Belum ada pertemuan tersedia.</p>
                ) : (
                  <div style={{ display: 'flex', flexDirection: 'column', gap: '0.65rem' }}>
                    {pertemuanList.map((p) => {
                      const isSelected = selectedPertemuan?.id === p.id
                      return (
                        <div
                          key={p.id}
                          onClick={() => viewPertemuan(p)}
                          style={{
                            padding: '0.85rem 1rem',
                            borderRadius: 'var(--radius-md)',
                            border: '1px solid',
                            borderColor: isSelected ? 'var(--accent)' : 'var(--border)',
                            background: isSelected ? 'var(--accent-light)' : 'var(--card)',
                            cursor: 'pointer',
                            transition: 'var(--transition)'
                          }}
                        >
                          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '0.25rem' }}>
                            <span className="badge badge-accent" style={{ fontSize: '0.7rem' }}>
                              Pertemuan {p.pertemuan_ke}
                            </span>
                            <span style={{ fontSize: '0.75rem', color: 'var(--muted)' }}>📅 {p.tanggal}</span>
                          </div>
                          <strong style={{ display: 'block', fontSize: '0.9rem', color: 'var(--text)' }}>
                            {p.topik}
                          </strong>
                          <small className="text-muted">{p.jadwal?.mapel?.nama} • {p.jadwal?.kelas?.nama}</small>
                        </div>
                      )
                    })}
                  </div>
                )}
              </Card>

              {/* Right Column: Files */}
              <Card
                title={selectedPertemuan ? `Pertemuan Ke-${selectedPertemuan.pertemuan_ke}: ${selectedPertemuan.topik}` : 'Materi Pembelajaran'}
                subtitle={selectedPertemuan ? `${selectedPertemuan.jadwal?.mapel?.nama || 'Mapel'} • Tanggal: ${selectedPertemuan.tanggal}` : 'Pilih salah satu pertemuan di samping'}
              >
                {selectedPertemuan?.catatan && (
                  <div
                    style={{
                      background: 'var(--card-subtle)',
                      padding: '0.85rem 1rem',
                      borderRadius: 'var(--radius-md)',
                      border: '1px solid var(--border)',
                      marginBottom: '1rem',
                      fontSize: '0.85rem',
                      color: 'var(--text-light)'
                    }}
                  >
                    <strong>Instruksi Guru:</strong> {selectedPertemuan.catatan}
                  </div>
                )}

                {jurnalPertemuan?.uraian_kegiatan && (
                  <div
                    style={{
                      background: 'var(--card-subtle)',
                      padding: '0.85rem 1rem',
                      borderRadius: 'var(--radius-md)',
                      border: '1px solid var(--border)',
                      borderLeft: '3px solid var(--accent)',
                      marginBottom: '1.25rem',
                      fontSize: '0.85rem',
                      color: 'var(--text)'
                    }}
                  >
                    <strong style={{ color: 'var(--accent)' }}>📖 Rangkuman Kegiatan Pembelajaran (Jurnal Guru):</strong>
                    <div style={{ marginTop: '0.35rem', whiteSpace: 'pre-line', lineHeight: 1.5, color: 'var(--text-light)' }}>
                      {jurnalPertemuan.uraian_kegiatan}
                    </div>
                  </div>
                )}

                <h4 style={{ fontSize: '0.95rem', fontWeight: 700, marginBottom: '0.75rem' }}>
                  Berkas yang Tersedia ({materiList.length})
                </h4>

                {loadingMateri ? (
                  <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--muted)' }}>
                    Memuat materi...
                  </div>
                ) : materiList.length === 0 ? (
                  <div className="empty-state" style={{ padding: '2rem', background: 'var(--card-subtle)', borderRadius: 'var(--radius-md)' }}>
                    <div className="empty-state-icon">📂</div>
                    <div className="empty-state-title">Belum ada file materi pada pertemuan ini</div>
                  </div>
                ) : (
                  <div style={{ display: 'flex', flexDirection: 'column', gap: '0.75rem' }}>
                    {materiList.map((m) => {
                      const fileUrl = m.file_path ? `${STORAGE_URL}${m.file_path}` : null
                      return (
                        <div
                          key={m.id}
                          style={{
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'space-between',
                            padding: '0.85rem 1rem',
                            border: '1px solid var(--border)',
                            borderRadius: 'var(--radius-md)',
                            background: 'var(--card)'
                          }}
                        >
                          <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem' }}>
                            <div
                              style={{
                                width: '40px',
                                height: '40px',
                                background: 'var(--accent-light)',
                                color: 'var(--accent-hover)',
                                borderRadius: 'var(--radius-sm)',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                fontWeight: 800,
                                fontSize: '0.75rem'
                              }}
                            >
                              FILE
                            </div>
                            <div>
                              <div style={{ fontWeight: 600, fontSize: '0.9rem' }}>{m.judul}</div>
                              {m.konten && <small className="text-muted">{m.konten}</small>}
                            </div>
                          </div>

                          {fileUrl && (
                            <a
                              href={fileUrl}
                              target="_blank"
                              rel="noreferrer"
                              className="btn btn-sm btn-primary"
                              style={{ textDecoration: 'none' }}
                            >
                              ⬇️ Unduh Berkas
                            </a>
                          )}
                        </div>
                      )
                    })}
                  </div>
                )}
              </Card>
            </div>
          </div>
        )}

        {/* TAB 4: TUGAS */}
        {activeTab === 'tugas' && (
          <div>
            <div className="page-header">
              <div>
                <h1 className="page-title">Penugasan Siswa</h1>
                <p className="page-subtitle">Daftar latihan praktik & instruksi pengumpulan tugas</p>
              </div>
            </div>

            {selectedPertemuan && (
              <Card className="mb-4">
                <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                  <label style={{ fontWeight: 600, fontSize: '0.875rem' }}>Sesi Pertemuan:</label>
                  <select
                    className="form-select"
                    style={{ maxWidth: '450px' }}
                    value={selectedPertemuan.id}
                    onChange={(e) => {
                      const p = pertemuanList.find((item) => String(item.id) === e.target.value)
                      if (p) viewPertemuan(p)
                    }}
                  >
                    {pertemuanList.map((p) => (
                      <option key={p.id} value={p.id}>
                        P{p.pertemuan_ke}: {p.topik} ({p.jadwal?.mapel?.nama})
                      </option>
                    ))}
                  </select>
                </div>
              </Card>
            )}

            <div className="grid grid-2">
              {tugasList.length === 0 ? (
                <div className="card" style={{ gridColumn: '1 / -1', textAlign: 'center', padding: '3rem' }}>
                  <div className="empty-state-icon">🎉</div>
                  <div className="empty-state-title">Tidak ada tugas pada sesi pertemuan ini</div>
                  <p className="text-muted" style={{ fontSize: '0.85rem' }}>Buka pertemuan lain untuk mengecek penugasan aktif.</p>
                </div>
              ) : (
                tugasList.map((t) => {
                  const isDeadlinePassed = new Date(t.deadline) < new Date()
                  return (
                    <div key={t.id} className="card card-hover">
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '0.75rem' }}>
                        <span className={`badge ${isDeadlinePassed ? 'badge-danger' : 'badge-accent'}`}>
                          {isDeadlinePassed ? 'Lewat Batas Waktu' : 'Tugas Aktif'}
                        </span>
                        <span className="badge badge-neutral">Max: {t.nilai_maksimal} Poin</span>
                      </div>

                      <h3 style={{ fontSize: '1.2rem', fontWeight: 700, marginBottom: '0.4rem' }}>
                        {t.judul}
                      </h3>

                      <p style={{ fontSize: '0.875rem', color: 'var(--text-light)', marginBottom: '1rem', whiteSpace: 'pre-line' }}>
                        {t.deskripsi}
                      </p>

                      <div
                        style={{
                          background: 'var(--card-subtle)',
                          padding: '0.65rem 0.85rem',
                          borderRadius: 'var(--radius-sm)',
                          border: '1px solid var(--border)',
                          fontSize: '0.8rem',
                          color: 'var(--muted)',
                          marginBottom: '1.25rem'
                        }}
                      >
                        ⏰ Batas Waktu: <strong>{t.deadline}</strong>
                      </div>

                      <Button
                        variant="primary"
                        size="md"
                        style={{ width: '100%' }}
                        icon="📤"
                        onClick={() => {
                          setSubmitModalTugas(t)
                          setSubmitError('')
                        }}
                      >
                        Kumpulkan Tugas Ini
                      </Button>
                    </div>
                  )
                })
              )}
            </div>
          </div>
        )}

        {/* TAB 5: PRESENSI & KEHADIRAN */}
        {activeTab === 'absensi' && (
          <div>
            <div className="page-header mb-4">
              <div>
                <div style={{ display: 'flex', alignItems: 'center', gap: '0.6rem', marginBottom: '0.25rem' }}>
                  <h1 className="page-title">Presensi & Kehadiran Siswa</h1>
                  <span className="badge badge-accent">Transparansi Akademik</span>
                </div>
                <p className="page-subtitle">
                  Pantau riwayat kehadiran Anda di seluruh pertemuan, rekapitulasi semester, atau cek matriks kelas.
                </p>
              </div>

              {/* Sub-tab Navigation */}
              <div style={{ display: 'inline-flex', background: 'var(--card-subtle)', padding: '3px', borderRadius: 'var(--radius-full)', border: '1px solid var(--border)', flexWrap: 'wrap', gap: '2px' }}>
                <button
                  type="button"
                  className={`btn btn-sm ${presensiSubTab === 'personal' ? 'btn-primary' : 'btn-ghost'}`}
                  onClick={() => setPresensiSubTab('personal')}
                  style={{ borderRadius: 'var(--radius-full)', padding: '0.35rem 0.85rem' }}
                >
                  👤 Riwayat Siswa Saya
                </button>
                <button
                  type="button"
                  className={`btn btn-sm ${presensiSubTab === 'matriks' ? 'btn-primary' : 'btn-ghost'}`}
                  onClick={() => setPresensiSubTab('matriks')}
                  style={{ borderRadius: 'var(--radius-full)', padding: '0.35rem 0.85rem' }}
                >
                  📊 Matriks Seluruh Pertemuan
                </button>
                <button
                  type="button"
                  className={`btn btn-sm ${presensiSubTab === 'sesi' ? 'btn-primary' : 'btn-ghost'}`}
                  onClick={() => setPresensiSubTab('sesi')}
                  style={{ borderRadius: 'var(--radius-full)', padding: '0.35rem 0.85rem' }}
                >
                  📅 Per Sesi Mengajar
                </button>
              </div>
            </div>

            {/* =========================================================================
                SUB-TAB 1: RIWAYAT SELURUH PERTEMUAN SISWA SAYA (PERSONAL VIEW)
                ========================================================================= */}
            {presensiSubTab === 'personal' && (
              <div>
                {/* Class Switcher for Student Attendance */}
                {kelasList.length > 1 && (
                  <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', marginBottom: '1rem', flexWrap: 'wrap' }}>
                    <span style={{ fontSize: '0.8rem', fontWeight: 800, color: 'var(--muted)', textTransform: 'uppercase' }}>🏫 Pilih Kelas:</span>
                    {kelasList.map((k) => (
                      <button
                        key={k.id}
                        type="button"
                        className={`btn btn-sm ${String(selectedKelasId) === String(k.id) ? 'btn-primary' : 'btn-secondary'}`}
                        onClick={() => handleStudentSelectKelas(String(k.id))}
                        style={{ fontWeight: 700 }}
                      >
                        Kelas {k.nama}
                      </button>
                    ))}
                  </div>
                )}

                {/* Student Selector Card */}
                <Card className="mb-4">
                  <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '1rem' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.85rem', flexWrap: 'wrap', flex: 1 }}>
                      <div style={{ width: '38px', height: '38px', borderRadius: '50%', background: 'var(--accent-light)', color: 'var(--accent)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 800, fontSize: '1.1rem', border: '1px solid var(--accent-border)' }}>
                        👤
                      </div>
                      <div>
                        <label style={{ fontWeight: 700, fontSize: '0.875rem', display: 'block' }}>
                          Pilih Akun Siswa:
                        </label>
                        <select
                          className="form-select"
                          value={selectedSiswaId || (matrixData?.siswa?.[0] ? String(matrixData.siswa[0].id) : '')}
                          onChange={(e) => {
                            const newId = e.target.value
                            setSelectedSiswaId(newId)
                            localStorage.setItem('cbt_student_id', newId)
                          }}
                          style={{ minWidth: '280px', maxWidth: '400px', fontSize: '0.85rem', padding: '0.45rem 0.75rem' }}
                        >
                          {(matrixData?.siswa || []).map((s) => (
                            <option key={s.id} value={s.id}>
                              {s.name} ({s.email})
                            </option>
                          ))}
                        </select>
                      </div>
                    </div>

                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', flexWrap: 'wrap' }}>
                      <span className="badge badge-accent" style={{ fontSize: '0.75rem' }}>
                        ✨ Tersimpan di Perangkat
                      </span>
                      <span style={{ fontSize: '0.8rem', color: 'var(--muted)' }}>
                        Total {matrixData?.total_pertemuan || 0} Pertemuan Terlaksana
                      </span>
                    </div>
                  </div>
                </Card>

                {!currentRekap ? (
                  <div className="empty-state" style={{ padding: '3rem' }}>
                    <p className="text-muted">Data rekapitulasi siswa belum tersedia.</p>
                  </div>
                ) : (
                  <>
                    {/* Eligibility & Overview Banner */}
                    <div
                      style={{
                        background: isEligibleExam
                          ? 'linear-gradient(135deg, #065f46 0%, #047857 100%)'
                          : 'linear-gradient(135deg, #92400e 0%, #b45309 100%)',
                          color: '#ffffff',
                          padding: '1.5rem',
                          borderRadius: 'var(--radius-lg)',
                          marginBottom: '1.5rem',
                          boxShadow: 'var(--shadow-md)'
                        }}
                      >
                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '1rem', marginBottom: '1rem' }}>
                          <div>
                            <div style={{ fontSize: '0.8rem', fontWeight: 600, opacity: 0.9, textTransform: 'uppercase', letterSpacing: '0.05em' }}>
                              KARTU RAPOR KEHADIRAN • {currentRekap.siswa?.name}
                            </div>
                            <h2 style={{ fontSize: '1.75rem', fontWeight: 800, margin: '0.35rem 0' }}>
                              Tingkat Kehadiran Sah: {currentStats.persentase || 0}%
                            </h2>
                            <p style={{ margin: 0, fontSize: '0.875rem', opacity: 0.95 }}>
                              {isMemenuhi
                                ? '🎉 Memenuhi kriteria kehadiran minimum (≥ 75%) untuk mengikuti Ujian Akhir / CBT.'
                                : '⚠️ Perhatian: Kehadiran di bawah 75%. Harap segera menghubungi Bu Yayu untuk konfirmasi atau tugas kompensasi.'}
                            </p>
                          </div>

                          <div style={{ display: 'flex', gap: '0.65rem', background: 'rgba(0,0,0,0.2)', padding: '0.75rem 1.25rem', borderRadius: 'var(--radius-md)', backdropFilter: 'blur(8px)' }}>
                            <div style={{ textAlign: 'center' }}>
                              <div style={{ fontSize: '1.25rem', fontWeight: 800 }}>{currentStats.hadir || 0}</div>
                              <div style={{ fontSize: '0.65rem', opacity: 0.85 }}>HADIR</div>
                            </div>
                            <div style={{ width: '1px', background: 'rgba(255,255,255,0.2)' }} />
                            <div style={{ textAlign: 'center' }}>
                              <div style={{ fontSize: '1.25rem', fontWeight: 800, color: '#e9d5ff' }}>{currentStats.dispen || 0}</div>
                              <div style={{ fontSize: '0.65rem', opacity: 0.85 }}>DISPEN</div>
                            </div>
                            <div style={{ width: '1px', background: 'rgba(255,255,255,0.2)' }} />
                            <div style={{ textAlign: 'center' }}>
                              <div style={{ fontSize: '1.25rem', fontWeight: 800 }}>{currentStats.izin || 0}</div>
                              <div style={{ fontSize: '0.65rem', opacity: 0.85 }}>IZIN</div>
                            </div>
                            <div style={{ width: '1px', background: 'rgba(255,255,255,0.2)' }} />
                            <div style={{ textAlign: 'center' }}>
                              <div style={{ fontSize: '1.25rem', fontWeight: 800 }}>{currentStats.sakit || 0}</div>
                              <div style={{ fontSize: '0.65rem', opacity: 0.85 }}>SAKIT</div>
                            </div>
                            <div style={{ width: '1px', background: 'rgba(255,255,255,0.2)' }} />
                            <div style={{ textAlign: 'center' }}>
                              <div style={{ fontSize: '1.25rem', fontWeight: 800, color: '#fecaca' }}>{currentStats.alpa || 0}</div>
                              <div style={{ fontSize: '0.65rem', opacity: 0.85 }}>ALPA</div>
                            </div>
                          </div>
                        </div>

                        {/* Visual Progress Bar */}
                        <div style={{ background: 'rgba(0,0,0,0.3)', height: '8px', borderRadius: 'var(--radius-full)', overflow: 'hidden' }}>
                          <div
                            style={{
                              width: `${Math.min(currentStats.persentase || 0, 100)}%`,
                              height: '100%',
                              background: isMemenuhi ? '#34d399' : '#fbbf24',
                              borderRadius: 'var(--radius-full)',
                              transition: 'width 0.6s cubic-bezier(0.4, 0, 0.2, 1)'
                            }}
                          />
                        </div>

                        {/* Educational Callout */}
                        <div style={{
                          marginTop: '0.85rem',
                          padding: '0.6rem 0.85rem',
                          background: 'rgba(255,255,255,0.12)',
                          borderRadius: 'var(--radius-sm)',
                          fontSize: '0.775rem',
                          display: 'flex',
                          alignItems: 'center',
                          gap: '0.5rem',
                          lineHeight: 1.4
                        }}>
                          <span>💡</span>
                          <span><b>Pedoman Resmi:</b> Kehadiran Sah dihitung dari total <b>Hadir (H)</b> + <b>Dispensasi (D)</b>. Jam kegiatan lomba atau tugas dinas sekolah diakui penuh sebagai kehadiran.</span>
                        </div>
                      </div>

                      {/* Detail Table of ALL Meetings for this Student */}
                      <Card
                        title={`Riwayat Kehadiran di Seluruh Pertemuan (${filteredDetails.length} dari ${allDetails.length} Sesi)`}
                        subtitle="Pencatatan resmi keterlaksanaan belajar mengajar Bahasa Indonesia bersama Bu Yayu"
                        action={
                          <div style={{ display: 'flex', gap: '0.35rem', flexWrap: 'wrap' }}>
                            {[
                              { key: 'semua', label: `Semua (${allDetails.length})` },
                              { key: 'hadir', label: `Hadir (${stats.hadir || 0})` },
                              { key: 'dispen', label: `Dispen (${stats.dispen || 0})` },
                              { key: 'izin_sakit', label: `Izin/Sakit (${(stats.izin || 0) + (stats.sakit || 0)})` },
                              { key: 'alpa', label: `Alpa (${stats.alpa || 0})` }
                            ].map((f) => (
                              <button
                                key={f.key}
                                type="button"
                                onClick={() => setStudentHistoryFilter(f.key)}
                                style={{
                                  fontSize: '0.75rem',
                                  padding: '0.25rem 0.6rem',
                                  borderRadius: 'var(--radius-full)',
                                  border: '1px solid var(--border)',
                                  background: studentHistoryFilter === f.key ? 'var(--accent)' : 'var(--card-subtle)',
                                  color: studentHistoryFilter === f.key ? '#ffffff' : 'var(--muted)',
                                  fontWeight: studentHistoryFilter === f.key ? 700 : 500,
                                  cursor: 'pointer',
                                  transition: 'all 0.15s ease'
                                }}
                              >
                                {f.label}
                              </button>
                            ))}
                          </div>
                        }
                      >
                        <div className="table-responsive">
                          <table className="table">
                            <thead>
                              <tr>
                                <th style={{ width: '50px', textAlign: 'center' }}>No</th>
                                <th style={{ width: '130px' }}>Pertemuan</th>
                                <th style={{ width: '120px' }}>Tanggal</th>
                                <th>Materi / Topik Pembahasan</th>
                                <th style={{ width: '120px', textAlign: 'center' }}>Status</th>
                                <th>Keterangan / Catatan</th>
                              </tr>
                            </thead>
                            <tbody>
                              {filteredDetails.length === 0 ? (
                                <tr>
                                  <td colSpan={6} style={{ textAlign: 'center', padding: '2rem' }} className="text-muted">
                                    Tidak ada catatan pertemuan dengan filter status yang dipilih.
                                  </td>
                                </tr>
                              ) : (
                                filteredDetails.map((dp, idx) => {
                                  const statusConfig = {
                                    hadir: { label: 'Hadir', bg: '#ecfdf5', color: '#047857', border: '#a7f3d0' },
                                    izin: { label: 'Izin', bg: '#eff6ff', color: '#1d4ed8', border: '#bfdbfe' },
                                    sakit: { label: 'Sakit', bg: '#fffbeb', color: '#b45309', border: '#fde68a' },
                                    dispen: { label: 'Dispen', bg: '#f5f3ff', color: '#6d28d9', border: '#ddd6fe' },
                                    alpa: { label: 'Alpa', bg: '#fef2f2', color: '#b91c1c', border: '#fecaca' }
                                  }
                                  const cfg = dp.status ? (statusConfig[dp.status] || statusConfig.hadir) : { label: 'Belum Terlaksana', bg: 'var(--card-subtle)', color: 'var(--muted)', border: 'var(--border)' }

                                  return (
                                    <tr key={dp.pertemuan_id || idx}>
                                      <td style={{ textAlign: 'center' }} className="text-muted">{idx + 1}</td>
                                      <td style={{ whiteSpace: 'nowrap' }}>
                                        <strong>Pertemuan {dp.pertemuan_ke}</strong>
                                      </td>
                                      <td style={{ fontSize: '0.825rem', whiteSpace: 'nowrap' }}>
                                        📅 {dp.tanggal}
                                      </td>
                                      <td>
                                        <span style={{ fontWeight: 600, color: 'var(--text)' }}>{dp.topik}</span>
                                      </td>
                                      <td style={{ textAlign: 'center' }}>
                                        <span
                                          style={{
                                            display: 'inline-block',
                                            padding: '0.25rem 0.65rem',
                                            borderRadius: 'var(--radius-sm)',
                                            fontSize: '0.75rem',
                                            fontWeight: 700,
                                            background: cfg.bg,
                                            color: cfg.color,
                                            border: `1px solid ${cfg.border}`,
                                            whiteSpace: 'nowrap'
                                          }}
                                        >
                                          {cfg.label}
                                        </span>
                                      </td>
                                      <td style={{ fontSize: '0.85rem', color: dp.keterangan ? 'var(--text)' : 'var(--muted)' }}>
                                        {dp.keterangan || '—'}
                                      </td>
                                    </tr>
                                  )
                                })
                              )}
                            </tbody>
                          </table>
                        </div>
                      </Card>
                    </>
                  )
                }
              </div>
            )}

            {/* =========================================================================
                SUB-TAB 2: MATRIKS PRESENSI SELURUH KELAS (ALL-MEETING MATRIX)
                ========================================================================= */}
            {presensiSubTab === 'matriks' && (
              <div>
                <Card
                  title={`Matriks Presensi Seluruh Pertemuan — ${matrixData?.pertemuan?.[0]?.jadwal?.kelas?.nama || 'Kelas XII PPLG'}`}
                  subtitle={`Mata Pelajaran: ${matrixData?.pertemuan?.[0]?.jadwal?.mapel?.nama || 'Bahasa Indonesia'} • Total: ${(matrixData?.pertemuan || []).length} Pertemuan`}
                  headerAction={
                    <Button
                      variant="primary"
                      size="sm"
                      icon="📊"
                      onClick={() => {
                        if (!matrixData) return
                        exportMatrixToExcel({
                          pertemuanList: matrixData.pertemuan || [],
                          rekapSiswa: matrixData.rekap_siswa || [],
                          kelasNama: matrixData.pertemuan?.[0]?.jadwal?.kelas?.nama || 'Kelas XII PPLG',
                          mapelNama: matrixData.pertemuan?.[0]?.jadwal?.mapel?.nama || 'Bahasa Indonesia',
                          guruNama: matrixData.pertemuan?.[0]?.jadwal?.guru?.name || 'Bu Yayu'
                        })
                      }}
                      title="Unduh Rekap Matriks Lengkap ke Excel"
                    >
                      Unduh Matriks Excel (.xls)
                    </Button>
                  }
                >
                  <div className="table-responsive" style={{ overflowX: 'auto' }}>
                    <table className="table" style={{ fontSize: '0.825rem' }}>
                      <thead>
                        <tr>
                          <th style={{ width: '40px', textAlign: 'center' }}>No</th>
                          <th className="table-sticky-col-name" style={{ minWidth: '180px' }}>Nama Siswa</th>
                          {(matrixData?.pertemuan || []).map((p) => (
                            <th key={p.id} style={{ textAlign: 'center', minWidth: '60px' }}>
                              <div>P{p.pertemuan_ke}</div>
                              <div style={{ fontSize: '0.65rem', fontWeight: 'normal', color: 'var(--muted)' }}>
                                {p.tanggal ? p.tanggal.slice(5) : ''}
                              </div>
                            </th>
                          ))}
                          <th style={{ width: '40px', textAlign: 'center', color: 'var(--accent)' }} title="Hadir">H</th>
                          <th style={{ width: '40px', textAlign: 'center', color: 'var(--info)' }} title="Izin">I</th>
                          <th style={{ width: '40px', textAlign: 'center', color: 'var(--warning)' }} title="Sakit">S</th>
                          <th style={{ width: '40px', textAlign: 'center', color: 'var(--purple)' }} title="Dispen">D</th>
                          <th style={{ width: '40px', textAlign: 'center', color: 'var(--danger)' }} title="Alpa">A</th>
                          <th style={{ width: '65px', textAlign: 'center' }}>% Sah</th>
                        </tr>
                      </thead>
                      <tbody>
                        {(matrixData?.rekap_siswa || []).map((r, idx) => (
                          <tr key={r.siswa?.id || idx}>
                            <td style={{ textAlign: 'center' }} className="text-muted">{idx + 1}</td>
                            <td className="table-sticky-col-name">
                              <strong>{r.siswa?.name || 'Siswa'}</strong>
                              <div style={{ fontSize: '0.7rem', color: 'var(--muted)' }}>{r.siswa?.email}</div>
                            </td>
                            {(matrixData?.pertemuan || []).map((p) => {
                              const st = r.kehadiran?.[p.id]?.status
                              const statusCfg = {
                                hadir: { symbol: 'H', bg: 'var(--accent-light)', color: 'var(--accent)' },
                                izin: { symbol: 'I', bg: 'var(--info-light)', color: 'var(--info)' },
                                sakit: { symbol: 'S', bg: 'var(--warning-light)', color: 'var(--warning)' },
                                dispen: { symbol: 'D', bg: 'var(--purple-light)', color: 'var(--purple)' },
                                alpa: { symbol: 'A', bg: 'var(--danger-light)', color: 'var(--danger)' }
                              }
                              const c = statusCfg[st] || { symbol: '—', bg: 'var(--card-subtle)', color: 'var(--muted)' }
                              return (
                                <td key={p.id} style={{ textAlign: 'center', padding: '0.4rem 0.2rem' }}>
                                  <span
                                    style={{
                                      display: 'inline-block',
                                      width: '26px',
                                      height: '26px',
                                      lineHeight: '26px',
                                      borderRadius: '4px',
                                      fontWeight: 800,
                                      fontSize: '0.75rem',
                                      background: c.bg,
                                      color: c.color,
                                      border: '1px solid transparent'
                                    }}
                                  >
                                    {c.symbol}
                                  </span>
                                </td>
                              )
                            })}
                            <td style={{ textAlign: 'center', fontWeight: 700, color: 'var(--accent)', background: 'var(--accent-light)' }}>{r.stats?.hadir || 0}</td>
                            <td style={{ textAlign: 'center', fontWeight: 700, color: 'var(--info)', background: 'var(--info-light)' }}>{r.stats?.izin || 0}</td>
                            <td style={{ textAlign: 'center', fontWeight: 700, color: 'var(--warning)', background: 'var(--warning-light)' }}>{r.stats?.sakit || 0}</td>
                            <td style={{ textAlign: 'center', fontWeight: 700, color: 'var(--purple)', background: 'var(--purple-light)' }}>{r.stats?.dispen || 0}</td>
                            <td style={{ textAlign: 'center', fontWeight: 700, color: 'var(--danger)', background: 'var(--danger-light)' }}>{r.stats?.alpa || 0}</td>
                            <td style={{ textAlign: 'center', fontWeight: 800, color: (r.stats?.persentase || 0) >= 80 ? 'var(--accent)' : 'var(--warning)' }}>
                              {r.stats?.persentase || 0}%
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </Card>
              </div>
            )}

            {/* =========================================================================
                SUB-TAB 3: PER SESI PERTEMUAN TUNGGAL (DETIL SATU PERTEMUAN)
                ========================================================================= */}
            {presensiSubTab === 'sesi' && (
              <div>
                <Card className="mb-4">
                  <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '1rem' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', flexWrap: 'wrap' }}>
                      <label style={{ fontWeight: 600, fontSize: '0.875rem' }}>Pilih Sesi Pertemuan:</label>
                      <select
                        className="form-select"
                        style={{ maxWidth: '450px' }}
                        value={selectedPertemuan?.id || ''}
                        onChange={(e) => {
                          const p = pertemuanList.find((item) => String(item.id) === e.target.value)
                          if (p) viewPertemuan(p)
                        }}
                      >
                        {pertemuanList.map((p) => (
                          <option key={p.id} value={p.id}>
                            P{p.pertemuan_ke}: {p.topik} — {p.jadwal?.kelas?.nama || 'Kelas'} ({p.tanggal})
                          </option>
                        ))}
                      </select>
                    </div>

                    <div style={{ display: 'flex', gap: '0.4rem', flexWrap: 'wrap' }}>
                      <Button
                        variant="secondary"
                        size="sm"
                        icon="📊"
                        onClick={() => {
                          if (!selectedPertemuan) return
                          exportToExcel({
                            pertemuan: selectedPertemuan,
                            data: absensiList.map((a, idx) => ({
                              no: idx + 1,
                              name: a.siswa?.name || 'Siswa',
                              email: a.siswa?.email || '-',
                              status: a.status || 'hadir',
                              keterangan: a.keterangan || ''
                            })),
                            summary: {
                              total: absensiList.length,
                              hadir: absensiList.filter((a) => a.status === 'hadir').length,
                              izin: absensiList.filter((a) => a.status === 'izin').length,
                              sakit: absensiList.filter((a) => a.status === 'sakit').length,
                              dispen: absensiList.filter((a) => a.status === 'dispen').length,
                              alpa: absensiList.filter((a) => a.status === 'alpa').length
                            }
                          })
                        }}
                      >
                        Excel (.xls)
                      </Button>
                      <Button
                        variant="secondary"
                        size="sm"
                        icon="📄"
                        onClick={() => {
                          if (!selectedPertemuan) return
                          exportToCSV({
                            pertemuan: selectedPertemuan,
                            data: absensiList.map((a, idx) => ({
                              no: idx + 1,
                              name: a.siswa?.name || 'Siswa',
                              email: a.siswa?.email || '-',
                              status: a.status || 'hadir',
                              keterangan: a.keterangan || ''
                            }))
                          })
                        }}
                      >
                        CSV
                      </Button>
                      <Button
                        variant="secondary"
                        size="sm"
                        icon="🖨️"
                        onClick={() => {
                          if (!selectedPertemuan) return
                          printAttendanceReport({
                            pertemuan: selectedPertemuan,
                            data: absensiList.map((a, idx) => ({
                              no: idx + 1,
                              name: a.siswa?.name || 'Siswa',
                              email: a.siswa?.email || '-',
                              status: a.status || 'hadir',
                              keterangan: a.keterangan || ''
                            })),
                            summary: {
                              total: absensiList.length,
                              hadir: absensiList.filter((a) => a.status === 'hadir').length,
                              izin: absensiList.filter((a) => a.status === 'izin').length,
                              sakit: absensiList.filter((a) => a.status === 'sakit').length,
                              dispen: absensiList.filter((a) => a.status === 'dispen').length,
                              alpa: absensiList.filter((a) => a.status === 'alpa').length
                            }
                          })
                        }}
                      >
                        Cetak / PDF
                      </Button>
                    </div>
                  </div>
                </Card>

                {/* Single Meeting Attendance Table */}
                <Card
                  title={`Data Kehadiran — Pertemuan ${selectedPertemuan?.pertemuan_ke || 1}`}
                  subtitle={`Topik: ${selectedPertemuan?.topik || '-'} • Tanggal: ${selectedPertemuan?.tanggal || '-'}`}
                >
                  {loadingAbsensi ? (
                    <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--muted)' }}>
                      Memuat data presensi sesi...
                    </div>
                  ) : absensiList.length === 0 ? (
                    <div className="empty-state" style={{ padding: '2.5rem' }}>
                      <div className="empty-state-icon">📋</div>
                      <div className="empty-state-title">Belum ada catatan presensi pada sesi ini</div>
                    </div>
                  ) : (
                    <div className="table-responsive">
                      <table className="table">
                        <thead>
                          <tr>
                            <th style={{ width: '60px' }}>No</th>
                            <th>Nama Siswa</th>
                            <th style={{ width: '150px' }}>Status</th>
                            <th>Keterangan</th>
                          </tr>
                        </thead>
                        <tbody>
                          {absensiList.map((a, idx) => {
                            const statusConfig = {
                              hadir: { label: 'Hadir', bg: '#ecfdf5', color: '#047857', border: '#a7f3d0' },
                              izin: { label: 'Izin', bg: '#eff6ff', color: '#1d4ed8', border: '#bfdbfe' },
                              sakit: { label: 'Sakit', bg: '#fffbeb', color: '#b45309', border: '#fde68a' },
                              dispen: { label: 'Dispensasi', bg: '#f5f3ff', color: '#6d28d9', border: '#ddd6fe' },
                              alpa: { label: 'Alpa', bg: '#fef2f2', color: '#b91c1c', border: '#fecaca' }
                            }
                            const cfg = statusConfig[a.status] || statusConfig.hadir
                            return (
                              <tr key={a.id || idx}>
                                <td className="text-muted">{idx + 1}</td>
                                <td>
                                  <strong>{a.siswa?.name || 'Siswa'}</strong>
                                  <div style={{ fontSize: '0.75rem', color: 'var(--muted)' }}>{a.siswa?.email}</div>
                                </td>
                                <td>
                                  <span
                                    style={{
                                      display: 'inline-block',
                                      padding: '0.25rem 0.65rem',
                                      borderRadius: 'var(--radius-sm)',
                                      fontSize: '0.75rem',
                                      fontWeight: 700,
                                      background: cfg.bg,
                                      color: cfg.color,
                                      border: `1px solid ${cfg.border}`
                                    }}
                                  >
                                    {cfg.label}
                                  </span>
                                </td>
                                <td style={{ fontSize: '0.85rem', color: a.keterangan ? 'var(--text)' : 'var(--muted)' }}>
                                  {a.keterangan || '—'}
                                </td>
                              </tr>
                            )
                          })}
                        </tbody>
                      </table>
                    </div>
                  )}
                </Card>
              </div>
            )}
          </div>
        )}
      </main>

      {/* Modal Kumpulkan Tugas Siswa */}
      <Modal
        isOpen={Boolean(submitModalTugas)}
        onClose={() => setSubmitModalTugas(null)}
        title={`Kumpulkan Tugas: ${submitModalTugas?.judul}`}
        footer={
          <>
            <Button variant="secondary" onClick={() => setSubmitModalTugas(null)} disabled={submitting}>
              Batal
            </Button>
            <Button variant="primary" onClick={handleStudentSubmit} loading={submitting}>
              Kirim Tugas
            </Button>
          </>
        }
      >
        <form onSubmit={handleStudentSubmit}>
          {submitError && (
            <div className="badge badge-danger mb-4" style={{ width: '100%', padding: '0.6rem' }}>
              ⚠️ {submitError}
            </div>
          )}

          <FormInput
            type="select"
            label="Nama Siswa Pengumpul (Pilih Nama Anda)"
            name="siswa_id"
            value={submitForm.siswa_id || selectedSiswaId}
            onChange={(e) => {
              setSubmitForm({ ...submitForm, siswa_id: e.target.value })
              setSelectedSiswaId(e.target.value)
              localStorage.setItem('cbt_student_id', e.target.value)
            }}
            options={(matrixData?.siswa || []).map((s) => ({
              value: s.id,
              label: `${s.name} (${s.email})`
            }))}
            required
            helperText="Pilih nama Anda untuk mencatat pengumpulan tugas tanpa perlu login"
          />

          <FormInput
            type="file"
            label="Lampiran File Tugas (ZIP, PDF, DOCX)"
            name="file"
            onChange={(e) => setSubmitForm({ ...submitForm, file: e.target.files[0] })}
            helperText="Maksimal ukuran file 50 MB"
            required
          />

          <FormInput
            type="textarea"
            label="Catatan atau Link Repository GitHub"
            name="catatan"
            value={submitForm.catatan}
            onChange={(e) => setSubmitForm({ ...submitForm, catatan: e.target.value })}
            placeholder="Tuliskan catatan pengerjaan atau pesan untuk guru..."
            rows={3}
          />
        </form>
      </Modal>

      {/* Footer */}
      <footer style={{ borderTop: '1px solid var(--border)', background: 'var(--card)', padding: '1.5rem', textAlign: 'center' }}>
        <div style={{ fontSize: '0.85rem', color: 'var(--muted)' }}>
          CBT PPLG &copy; {new Date().getFullYear()} • Sistem Informasi Pembelajaran & Administrasi Guru Siswa
        </div>
      </footer>
    </div>
  )
}
