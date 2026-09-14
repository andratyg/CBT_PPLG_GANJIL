import React, { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import api from '../api'
import Card from '../components/Card'
import Button from '../components/Button'

export default function Dashboard({ user }) {
  const [data, setData] = useState(null)
  const [jadwal, setJadwal] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    Promise.allSettled([
      api.get('/dashboard'),
      api.get('/jadwal')
    ])
      .then(([dashRes, jadwalRes]) => {
        if (dashRes.status === 'fulfilled') setData(dashRes.value.data)
        if (jadwalRes.status === 'fulfilled') setJadwal(jadwalRes.value.data || [])
      })
      .catch(console.error)
      .finally(() => setLoading(false))
  }, [])

  const isGuru = user?.role === 'guru'

  return (
    <div>
      <div className="page-header">
        <div>
          <h1 className="page-title">
            <span>Selamat Datang, {user?.name || 'Bapak/Ibu Guru'}!</span>
            <span style={{ fontSize: '1.25rem' }}>👋</span>
          </h1>
          <p className="page-subtitle">
            {isGuru
              ? 'Kelola presensi, materi ajar, penugasan, dan jurnal mengajar seluruh kelas binaan Anda.'
              : 'Pantau materi pembelajaran, tugas, dan rekap nilai Anda di sini.'}
          </p>
        </div>
        {isGuru && (
          <div className="flex gap-2">
            <Link to="/pertemuan">
              <Button variant="primary" icon="➕">Tambah Pertemuan</Button>
            </Link>
            <Link to="/absensi">
              <Button variant="outline" icon="📝">Presensi Cepat</Button>
            </Link>
          </div>
        )}
      </div>

      {/* Stats Overview */}
      <div className="grid grid-4 mb-6">
        {isGuru ? (
          <>
            <div className="stat-card">
              <div className="stat-icon green">📚</div>
              <div>
                <div className="stat-number">{data?.total_mapel ?? 0}</div>
                <div className="stat-label">Mata Pelajaran</div>
              </div>
            </div>

            <div className="stat-card">
              <div className="stat-icon blue">👥</div>
              <div>
                <div className="stat-number">{data?.total_siswa ?? 0}</div>
                <div className="stat-label">Total Siswa Aktif</div>
              </div>
            </div>

            <div className="stat-card">
              <div className="stat-icon purple">🗓️</div>
              <div>
                <div className="stat-number">{data?.total_pertemuan ?? 0}</div>
                <div className="stat-label">Pertemuan Selesai</div>
              </div>
            </div>

            <div className="stat-card">
              <div className="stat-icon amber">⏳</div>
              <div>
                <div className="stat-number">{data?.total_tugas_belum_dinilai ?? 0}</div>
                <div className="stat-label">Tugas Perlu Dinilai</div>
              </div>
            </div>
          </>
        ) : (
          <>
            <div className="stat-card">
              <div className="stat-icon blue">🏫</div>
              <div>
                <div className="stat-number">{data?.kelas?.length ?? 0}</div>
                <div className="stat-label">Kelas Terdaftar</div>
              </div>
            </div>

            <div className="stat-card">
              <div className="stat-icon amber">⏳</div>
              <div>
                <div className="stat-number">{data?.tugas_pending ?? 0}</div>
                <div className="stat-label">Tugas Perlu Dikerjakan</div>
              </div>
            </div>

            <div className="stat-card">
              <div className="stat-icon green">✅</div>
              <div>
                <div className="stat-number">{data?.tugas_terkirim ?? 0}</div>
                <div className="stat-label">Tugas Dikumpulkan</div>
              </div>
            </div>

            <div className="stat-card">
              <div className="stat-icon purple">⭐</div>
              <div>
                <div className="stat-number">{data?.nilai_rata ?? '–'}</div>
                <div className="stat-label">Nilai Rata-rata</div>
              </div>
            </div>
          </>
        )}
      </div>

      <div className="grid grid-2">
        {/* Quick Shortcuts / Today's Focus */}
        <Card
          title="Akses Cepat Modul"
          subtitle="Pintasan administrasi pembelajaran seluruh program keahlian"
        >
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(130px, 1fr))', gap: '0.85rem' }}>
            <Link to="/absensi" style={{ textDecoration: 'none' }}>
              <div
                className="card card-hover"
                style={{
                  textAlign: 'center',
                  padding: '1.15rem 0.75rem',
                  background: 'var(--card-subtle)',
                  border: '1px solid var(--border)',
                  borderRadius: 'var(--radius-md)',
                  transition: 'var(--transition)'
                }}
              >
                <div style={{ fontSize: '1.85rem', marginBottom: '0.45rem' }}>📋</div>
                <div style={{ fontWeight: 700, fontSize: '0.875rem', color: 'var(--text)' }}>Presensi</div>
                <small className="text-muted" style={{ fontSize: '0.725rem' }}>Cek Kehadiran</small>
              </div>
            </Link>

            <Link to="/pertemuan" style={{ textDecoration: 'none' }}>
              <div
                className="card card-hover"
                style={{
                  textAlign: 'center',
                  padding: '1.15rem 0.75rem',
                  background: 'var(--card-subtle)',
                  border: '1px solid var(--border)',
                  borderRadius: 'var(--radius-md)',
                  transition: 'var(--transition)'
                }}
              >
                <div style={{ fontSize: '1.85rem', marginBottom: '0.45rem' }}>📁</div>
                <div style={{ fontWeight: 700, fontSize: '0.875rem', color: 'var(--text)' }}>Materi</div>
                <small className="text-muted" style={{ fontSize: '0.725rem' }}>Upload & Modul</small>
              </div>
            </Link>

            <Link to="/tugas" style={{ textDecoration: 'none' }}>
              <div
                className="card card-hover"
                style={{
                  textAlign: 'center',
                  padding: '1.15rem 0.75rem',
                  background: 'var(--card-subtle)',
                  border: '1px solid var(--border)',
                  borderRadius: 'var(--radius-md)',
                  transition: 'var(--transition)'
                }}
              >
                <div style={{ fontSize: '1.85rem', marginBottom: '0.45rem' }}>📝</div>
                <div style={{ fontWeight: 700, fontSize: '0.875rem', color: 'var(--text)' }}>Tugas</div>
                <small className="text-muted" style={{ fontSize: '0.725rem' }}>Penugasan & Nilai</small>
              </div>
            </Link>

            <Link to="/jurnal" style={{ textDecoration: 'none' }}>
              <div
                className="card card-hover"
                style={{
                  textAlign: 'center',
                  padding: '1.15rem 0.75rem',
                  background: 'var(--card-subtle)',
                  border: '1px solid var(--border)',
                  borderRadius: 'var(--radius-md)',
                  transition: 'var(--transition)'
                }}
              >
                <div style={{ fontSize: '1.85rem', marginBottom: '0.45rem' }}>📖</div>
                <div style={{ fontWeight: 700, fontSize: '0.875rem', color: 'var(--text)' }}>Jurnal</div>
                <small className="text-muted" style={{ fontSize: '0.725rem' }}>Catatan Mengajar</small>
              </div>
            </Link>
          </div>
        </Card>

        {/* Schedule Preview */}
        <Card
          title="Jadwal Pelajaran"
          subtitle="Jadwal mengajar dan agenda kelas aktif"
          action={
            <Link to="/jadwal" style={{ fontSize: '0.825rem', color: 'var(--accent)', fontWeight: 700, textDecoration: 'none' }}>
              Lihat Semua &rarr;
            </Link>
          }
        >
          {loading ? (
            <div style={{ padding: '1.5rem', textAlign: 'center', color: 'var(--muted)' }}>
              Memuat jadwal...
            </div>
          ) : jadwal.length === 0 ? (
            <div className="empty-state" style={{ padding: '1.5rem' }}>
              <div className="empty-state-icon">📅</div>
              <div className="empty-state-title">Belum ada jadwal terdaftar</div>
              <p className="text-muted" style={{ fontSize: '0.825rem' }}>Tambahkan jadwal di menu Jadwal Mengajar.</p>
            </div>
          ) : (
            <div style={{ display: 'flex', flexDirection: 'column', gap: '0.65rem' }}>
              {jadwal.slice(0, 4).map((j) => (
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
                  <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem' }}>
                    <span className="badge badge-accent" style={{ minWidth: '60px', justifyContent: 'center' }}>
                      {j.hari}
                    </span>
                    <div>
                      <div style={{ fontWeight: 600, fontSize: '0.875rem' }}>{j.mapel?.nama || 'Mata Pelajaran'}</div>
                      <small className="text-muted">Kelas {j.kelas?.nama || '–'}</small>
                    </div>
                  </div>
                  <div style={{ fontSize: '0.8rem', fontWeight: 600, color: 'var(--muted)' }}>
                    {j.jam_mulai} – {j.jam_selesai}
                  </div>
                </div>
              ))}
            </div>
          )}
        </Card>
      </div>
    </div>
  )
}
