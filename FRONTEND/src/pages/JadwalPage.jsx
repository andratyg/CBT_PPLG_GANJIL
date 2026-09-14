import React, { useState, useEffect, useMemo } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../api'
import Button from '../components/Button'
import Modal from '../components/Modal'
import FormInput from '../components/FormInput'
import { getJurusanInfo } from '../utils/jurusan'

const DAYS = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']

export default function JadwalPage({ user }) {
  const navigate = useNavigate()
  const [jadwalList, setJadwalList] = useState([])
  const [kelasList, setKelasList] = useState([])
  const [mapelList, setMapelList] = useState([])
  const [loading, setLoading] = useState(true)

  // Filters
  const [activeDay, setActiveDay] = useState('semua')
  const [activeJurusan, setActiveJurusan] = useState('semua')
  const [activeTingkat, setActiveTingkat] = useState('semua')
  const [searchQuery, setSearchQuery] = useState('')

  // Modals
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [editingItem, setEditingItem] = useState(null)
  const [itemToDelete, setItemToDelete] = useState(null)
  const [submitting, setSubmitting] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

  const [form, setForm] = useState({
    kelas_id: '',
    mapel_id: '',
    guru_id: user?.id || '',
    hari: 'senin',
    jam_mulai: '07:30',
    jam_selesai: '09:30'
  })

  const isGuru = user?.role === 'guru'

  const loadData = () => {
    setLoading(true)
    Promise.allSettled([
      api.get('/jadwal'),
      api.get('/kelas'),
      api.get('/mapel')
    ])
      .then(([jRes, kRes, mRes]) => {
        if (jRes.status === 'fulfilled') setJadwalList(jRes.value.data || [])
        if (kRes.status === 'fulfilled') setKelasList(kRes.value.data || [])
        if (mRes.status === 'fulfilled') setMapelList(mRes.value.data || [])
      })
      .catch(console.error)
      .finally(() => setLoading(false))
  }

  useEffect(() => {
    loadData()
  }, [])

  const openAddModal = () => {
    setEditingItem(null)
    setForm({
      kelas_id: kelasList[0]?.id || '',
      mapel_id: mapelList[0]?.id || '',
      guru_id: user?.id || '',
      hari: 'senin',
      jam_mulai: '07:30',
      jam_selesai: '09:30'
    })
    setErrorMsg('')
    setIsModalOpen(true)
  }

  const openEditModal = (item) => {
    setEditingItem(item)
    setForm({
      kelas_id: item.kelas_id,
      mapel_id: item.mapel_id,
      guru_id: item.guru_id,
      hari: item.hari,
      jam_mulai: item.jam_mulai?.substring(0, 5) || '07:30',
      jam_selesai: item.jam_selesai?.substring(0, 5) || '09:30'
    })
    setErrorMsg('')
    setIsModalOpen(true)
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSubmitting(true)
    setErrorMsg('')
    try {
      const payload = {
        kelas_id: Number(form.kelas_id),
        mapel_id: Number(form.mapel_id),
        guru_id: Number(form.guru_id) || user?.id,
        hari: form.hari.toLowerCase(),
        jam_mulai: form.jam_mulai,
        jam_selesai: form.jam_selesai
      }

      if (editingItem) {
        await api.put(`/jadwal/${editingItem.id}`, payload)
      } else {
        await api.post('/jadwal', payload)
      }

      setIsModalOpen(false)
      loadData()
    } catch (err) {
      console.error(err)
      setErrorMsg(err.response?.data?.message || 'Gagal menyimpan data jadwal.')
    } finally {
      setSubmitting(false)
    }
  }

  const handleDelete = async () => {
    if (!itemToDelete) return
    setSubmitting(true)
    try {
      await api.delete(`/jadwal/${itemToDelete.id}`)
      setItemToDelete(null)
      loadData()
    } catch (err) {
      console.error(err)
      alert('Gagal menghapus jadwal.')
    } finally {
      setSubmitting(false)
    }
  }

  // Extract distinct departments from loaded kelasList & jadwalList
  const distinctJurusan = useMemo(() => {
    const set = new Set()
    jadwalList.forEach((j) => {
      const info = getJurusanInfo(j.kelas?.nama)
      if (info.code) set.add(info.code)
    })
    return ['semua', ...Array.from(set)]
  }, [jadwalList])

  // Filtered List
  const filteredJadwal = useMemo(() => {
    return jadwalList.filter((j) => {
      // Filter Hari
      if (activeDay !== 'semua' && j.hari?.toLowerCase() !== activeDay.toLowerCase()) {
        return false
      }
      // Filter Jurusan
      if (activeJurusan !== 'semua') {
        const info = getJurusanInfo(j.kelas?.nama)
        if (info.code !== activeJurusan) return false
      }
      // Filter Tingkat
      if (activeTingkat !== 'semua') {
        const kelasNama = (j.kelas?.nama || '').toUpperCase()
        if (activeTingkat === 'X' && !kelasNama.startsWith('X ')) return false
        if (activeTingkat === 'XI' && !kelasNama.startsWith('XI ')) return false
        if (activeTingkat === 'XII' && !kelasNama.startsWith('XII ')) return false
      }
      // Filter Search
      if (searchQuery.trim()) {
        const q = searchQuery.toLowerCase()
        const mapel = (j.mapel?.nama || '').toLowerCase()
        const kelas = (j.kelas?.nama || '').toLowerCase()
        const guru = (j.guru?.name || '').toLowerCase()
        if (!mapel.includes(q) && !kelas.includes(q) && !guru.includes(q)) {
          return false
        }
      }
      return true
    })
  }, [jadwalList, activeDay, activeJurusan, activeTingkat, searchQuery])

  // Count by Jurusan for pills
  const getCountJurusan = (code) => {
    if (code === 'semua') return jadwalList.length
    return jadwalList.filter((j) => getJurusanInfo(j.kelas?.nama).code === code).length
  }

  const todayStr = new Date().toLocaleDateString('id-ID', { weekday: 'long' }).toLowerCase()

  return (
    <div style={{ maxWidth: 1280, margin: '0 auto' }}>
      {/* Page Header */}
      <div className="page-header" style={{ marginBottom: '1.5rem' }}>
        <div>
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem', flexWrap: 'wrap' }}>
            <h1 className="page-title" style={{ margin: 0 }}>
              <span>Jadwal Pelajaran & Mengajar</span>
            </h1>
            <span className="badge badge-accent" style={{ fontWeight: 800 }}>
              {jadwalList.length} Sesi Terdaftar
            </span>
            <span className="badge badge-neutral" style={{ fontSize: '0.75rem' }}>
              Lintas Kejuruan: PPLG • TKJ • DKV • MPLB
            </span>
          </div>
          <p className="page-subtitle" style={{ marginTop: '0.35rem' }}>
            Agenda pembelajaran mingguan seluruh kelas dan program keahlian binaan guru
          </p>
        </div>
        {isGuru && (
          <Button variant="primary" icon="➕" onClick={openAddModal}>
            Tambah Jadwal
          </Button>
        )}
      </div>

      {/* Control Studio Bar for Filters */}
      <div
        className="card"
        style={{
          marginBottom: '1.5rem',
          padding: '1.15rem 1.25rem',
          background: 'var(--surface)',
          border: '1px solid var(--border)',
          borderRadius: 'var(--radius-lg)',
          boxShadow: 'var(--shadow-sm)'
        }}
      >
        {/* Row 1: Filter Jurusan (Department Tabs) */}
        <div style={{ marginBottom: '1rem' }}>
          <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '0.5rem' }}>
            <span style={{ fontSize: '0.75rem', fontWeight: 800, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--muted)' }}>
              🎯 Filter Program Keahlian / Jurusan:
            </span>
            {(activeJurusan !== 'semua' || activeDay !== 'semua' || activeTingkat !== 'semua' || searchQuery) && (
              <button
                type="button"
                onClick={() => {
                  setActiveJurusan('semua')
                  setActiveDay('semua')
                  setActiveTingkat('semua')
                  setSearchQuery('')
                }}
                style={{
                  background: 'transparent',
                  border: 'none',
                  color: 'var(--accent)',
                  cursor: 'pointer',
                  fontWeight: 700,
                  fontSize: '0.75rem'
                }}
              >
                ↺ Reset Semua Filter
              </button>
            )}
          </div>
          <div style={{ display: 'flex', gap: '0.5rem', flexWrap: 'wrap' }}>
            {distinctJurusan.map((code) => {
              const isActive = activeJurusan === code
              const count = getCountJurusan(code)
              let label = 'Semua Jurusan'
              let icon = '🏫'
              let activeBg = 'var(--accent)'
              let activeColor = '#ffffff'

              if (code === 'PPLG') {
                label = 'PPLG (Software & Gim)'
                icon = '💻'
                activeBg = '#4f46e5'
              } else if (code === 'TKJ') {
                label = 'TKJ (Jaringan & Komputer)'
                icon = '🌐'
                activeBg = '#0d9488'
              } else if (code === 'DKV') {
                label = 'DKV (Desain Komunikasi)'
                icon = '🎨'
                activeBg = '#d946ef'
              } else if (code === 'MPLB') {
                label = 'MPLB (Perkantoran & Bisnis)'
                icon = '📋'
                activeBg = '#d97706'
              }

              return (
                <button
                  key={code}
                  type="button"
                  onClick={() => setActiveJurusan(code)}
                  style={{
                    display: 'inline-flex',
                    alignItems: 'center',
                    gap: '0.4rem',
                    padding: '0.45rem 0.85rem',
                    borderRadius: 'var(--radius-full)',
                    fontSize: '0.8rem',
                    fontWeight: isActive ? 800 : 600,
                    cursor: 'pointer',
                    transition: 'all 0.15s ease',
                    border: isActive ? `1px solid ${activeBg}` : '1px solid var(--border)',
                    background: isActive ? activeBg : 'var(--surface-alt)',
                    color: isActive ? activeColor : 'var(--text)'
                  }}
                >
                  <span>{icon}</span>
                  <span>{label}</span>
                  <span
                    style={{
                      fontSize: '0.7rem',
                      padding: '0.1rem 0.4rem',
                      borderRadius: 'var(--radius-full)',
                      background: isActive ? 'rgba(255,255,255,0.25)' : 'var(--border)',
                      color: isActive ? '#ffffff' : 'var(--muted)',
                      fontWeight: 800
                    }}
                  >
                    {count}
                  </span>
                </button>
              )
            })}
          </div>
        </div>

        {/* Row 2: Filter Hari & Tingkat & Search */}
        <div style={{ display: 'grid', gridTemplateColumns: 'auto auto 1fr', gap: '1rem', alignItems: 'center', flexWrap: 'wrap' }}>
          {/* Hari Chips */}
          <div style={{ display: 'flex', gap: '0.35rem', overflowX: 'auto', paddingBottom: '0.2rem' }}>
            <button
              type="button"
              className={`btn btn-sm ${activeDay === 'semua' ? 'btn-primary' : 'btn-secondary'}`}
              onClick={() => setActiveDay('semua')}
              style={{ borderRadius: 'var(--radius-full)', padding: '0.3rem 0.75rem', fontSize: '0.775rem' }}
            >
              Semua Hari
            </button>
            {DAYS.map((d) => {
              const count = jadwalList.filter((j) => {
                if (j.hari?.toLowerCase() !== d) return false
                if (activeJurusan !== 'semua') {
                  return getJurusanInfo(j.kelas?.nama).code === activeJurusan
                }
                return true
              }).length

              const isToday = d === todayStr
              const isSelected = activeDay === d
              return (
                <button
                  key={d}
                  type="button"
                  className={`btn btn-sm ${isSelected ? 'btn-primary' : 'btn-secondary'}`}
                  onClick={() => setActiveDay(d)}
                  style={{
                    borderRadius: 'var(--radius-full)',
                    padding: '0.3rem 0.75rem',
                    fontSize: '0.775rem',
                    textTransform: 'capitalize',
                    display: 'inline-flex',
                    alignItems: 'center',
                    gap: '0.3rem'
                  }}
                >
                  <span>{d}</span>
                  {count > 0 && <span style={{ opacity: 0.85, fontSize: '0.7rem' }}>({count})</span>}
                  {isToday && (
                    <span
                      style={{
                        fontSize: '0.625rem',
                        background: isSelected ? '#ffffff' : 'var(--accent)',
                        color: isSelected ? '#000000' : '#ffffff',
                        padding: '0.05rem 0.35rem',
                        borderRadius: 'var(--radius-full)',
                        fontWeight: 800
                      }}
                    >
                      Hari Ini
                    </span>
                  )}
                </button>
              )
            })}
          </div>

          {/* Tingkat Kelas Filter */}
          <div style={{ display: 'flex', gap: '0.3rem' }}>
            {['semua', 'X', 'XI', 'XII'].map((t) => (
              <button
                key={t}
                type="button"
                onClick={() => setActiveTingkat(t)}
                style={{
                  padding: '0.3rem 0.65rem',
                  borderRadius: 'var(--radius-md)',
                  fontSize: '0.75rem',
                  fontWeight: 700,
                  cursor: 'pointer',
                  border: activeTingkat === t ? '1px solid var(--accent)' : '1px solid var(--border)',
                  background: activeTingkat === t ? 'var(--accent-subtle, rgba(59, 130, 246, 0.15))' : 'transparent',
                  color: activeTingkat === t ? 'var(--accent)' : 'var(--muted)'
                }}
              >
                {t === 'semua' ? 'Semua Tingkat' : `Kelas ${t}`}
              </button>
            ))}
          </div>

          {/* Search Box */}
          <div style={{ minWidth: 200 }}>
            <input
              type="text"
              className="form-control"
              placeholder="🔍 Cari mapel, kelas, guru..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              style={{
                width: '100%',
                padding: '0.35rem 0.75rem',
                fontSize: '0.8rem',
                borderRadius: 'var(--radius-full)'
              }}
            />
          </div>
        </div>
      </div>

      {/* Schedule Cards Grid */}
      <div className="grid grid-3" style={{ gap: '1.25rem' }}>
        {loading ? (
          <div className="card" style={{ gridColumn: '1 / -1', textAlign: 'center', padding: '3.5rem' }}>
            <span className="text-muted">Memuat data jadwal pelajaran lintas program keahlian...</span>
          </div>
        ) : filteredJadwal.length === 0 ? (
          <div className="card" style={{ gridColumn: '1 / -1', textAlign: 'center', padding: '3.5rem' }}>
            <div className="empty-state-icon" style={{ fontSize: '3rem', marginBottom: '0.75rem' }}>🗓️</div>
            <div className="empty-state-title" style={{ fontSize: '1.2rem', fontWeight: 800 }}>
              Tidak ada jadwal ditemukan
            </div>
            <p className="text-muted" style={{ fontSize: '0.875rem', marginTop: '0.5rem', maxWidth: 460, margin: '0.5rem auto 1.25rem' }}>
              Tidak ada jadwal KBM yang cocok dengan kriteria filter yang Anda pilih
              {activeDay !== 'semua' ? ` pada hari ${activeDay}` : ''}
              {activeJurusan !== 'semua' ? ` jurusan ${activeJurusan}` : ''}.
            </p>
            <Button
              variant="outline"
              size="sm"
              onClick={() => {
                setActiveJurusan('semua')
                setActiveDay('semua')
                setActiveTingkat('semua')
                setSearchQuery('')
              }}
            >
              Tampilkan Semua Jadwal
            </Button>
          </div>
        ) : (
          filteredJadwal.map((j) => {
            const jur = getJurusanInfo(j.kelas?.nama)
            const isToday = j.hari?.toLowerCase() === todayStr

            return (
              <div
                key={j.id}
                className="card card-hover"
                style={{
                  display: 'flex',
                  flexDirection: 'column',
                  justifyContent: 'space-between',
                  position: 'relative',
                  borderTop: `4px solid ${jur.color}`,
                  overflow: 'hidden'
                }}
              >
                {/* Card Top Row: Jurusan Badge & Day Pill */}
                <div>
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '0.85rem' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.4rem', flexWrap: 'wrap' }}>
                      <span
                        style={{
                          display: 'inline-flex',
                          alignItems: 'center',
                          gap: '0.25rem',
                          background: jur.bg,
                          border: `1px solid ${jur.border}`,
                          color: jur.color,
                          fontSize: '0.725rem',
                          fontWeight: 800,
                          padding: '0.2rem 0.55rem',
                          borderRadius: 'var(--radius-md)'
                        }}
                      >
                        <span>{jur.icon}</span>
                        <span>{jur.code}</span>
                      </span>

                      <span className="badge badge-accent" style={{ textTransform: 'capitalize', fontSize: '0.725rem' }}>
                        {j.hari}
                      </span>

                      {isToday && (
                        <span
                          className="badge"
                          style={{
                            background: '#047857',
                            color: '#ffffff',
                            fontSize: '0.675rem',
                            fontWeight: 800,
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '0.2rem'
                          }}
                        >
                          🌟 Hari Ini
                        </span>
                      )}
                    </div>

                    <span
                      style={{
                        fontSize: '0.775rem',
                        fontWeight: 800,
                        color: 'var(--text)',
                        background: 'var(--surface-alt)',
                        padding: '0.2rem 0.5rem',
                        borderRadius: 'var(--radius-sm)',
                        border: '1px solid var(--border)'
                      }}
                    >
                      ⏰ {j.jam_mulai?.substring(0, 5)} – {j.jam_selesai?.substring(0, 5)}
                    </span>
                  </div>

                  {/* Subject Title */}
                  <h3
                    style={{
                      fontSize: '1.05rem',
                      fontWeight: 800,
                      color: 'var(--text)',
                      marginBottom: '0.5rem',
                      lineHeight: 1.35
                    }}
                  >
                    {j.mapel?.nama || `Mapel #${j.mapel_id}`}
                  </h3>

                  {/* Target Class with department details */}
                  <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', marginBottom: '0.75rem', flexWrap: 'wrap' }}>
                    <span
                      style={{
                        display: 'inline-flex',
                        alignItems: 'center',
                        gap: '0.35rem',
                        background: 'var(--surface-alt)',
                        border: '1px solid var(--border)',
                        color: 'var(--text)',
                        fontSize: '0.8rem',
                        fontWeight: 700,
                        padding: '0.25rem 0.65rem',
                        borderRadius: 'var(--radius-md)'
                      }}
                    >
                      <span>🏫</span>
                      <span>Kelas {j.kelas?.nama || j.kelas_id}</span>
                    </span>

                    <span style={{ fontSize: '0.75rem', color: 'var(--muted)' }}>
                      {jur.name}
                    </span>
                  </div>

                  {/* Teacher Row */}
                  <div
                    style={{
                      display: 'flex',
                      alignItems: 'center',
                      gap: '0.4rem',
                      fontSize: '0.8rem',
                      color: 'var(--muted)',
                      marginBottom: '1rem',
                      padding: '0.4rem 0.6rem',
                      background: 'var(--surface-alt)',
                      borderRadius: 'var(--radius-md)'
                    }}
                  >
                    <span>👨‍🏫 Guru Pengampu:</span>
                    <strong style={{ color: 'var(--text)' }}>{j.guru?.name || `ID #${j.guru_id}`}</strong>
                  </div>
                </div>

                {/* Footer Action Buttons */}
                <div
                  style={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between',
                    paddingTop: '0.75rem',
                    borderTop: '1px solid var(--border)',
                    marginTop: '0.5rem'
                  }}
                >
                  <Button
                    size="sm"
                    variant="ghost"
                    onClick={() => {
                      // Direct link to Presensi with this class
                      navigate(`/absensi?kelas_id=${j.kelas_id}`)
                    }}
                    style={{ fontSize: '0.75rem', padding: '0.3rem 0.6rem', color: 'var(--accent)' }}
                  >
                    📋 Presensi Kelas
                  </Button>

                  {isGuru && (
                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.35rem' }}>
                      <Button size="sm" variant="outline" onClick={() => openEditModal(j)} style={{ fontSize: '0.75rem', padding: '0.25rem 0.55rem' }}>
                        ✏️ Edit
                      </Button>
                      <Button size="sm" variant="danger" onClick={() => setItemToDelete(j)} style={{ fontSize: '0.75rem', padding: '0.25rem 0.5rem' }}>
                        🗑️
                      </Button>
                    </div>
                  )}
                </div>
              </div>
            )
          })
        )}
      </div>

      {/* Modal Tambah / Edit Jadwal */}
      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title={editingItem ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Baru'}
        footer={
          <>
            <Button variant="secondary" onClick={() => setIsModalOpen(false)} disabled={submitting}>
              Batal
            </Button>
            <Button variant="primary" onClick={handleSubmit} loading={submitting}>
              {editingItem ? 'Perbarui Jadwal' : 'Simpan Jadwal'}
            </Button>
          </>
        }
      >
        <form onSubmit={handleSubmit}>
          {errorMsg && (
            <div className="badge badge-danger mb-4" style={{ width: '100%', padding: '0.6rem' }}>
              ⚠️ {errorMsg}
            </div>
          )}

          <FormInput
            type="select"
            label="Mata Pelajaran"
            name="mapel_id"
            value={form.mapel_id}
            onChange={(e) => setForm({ ...form, mapel_id: e.target.value })}
            options={mapelList.map((m) => ({ value: m.id, label: m.nama }))}
            required
          />

          <FormInput
            type="select"
            label="Kelas & Program Keahlian"
            name="kelas_id"
            value={form.kelas_id}
            onChange={(e) => setForm({ ...form, kelas_id: e.target.value })}
            options={kelasList.map((k) => {
              const info = getJurusanInfo(k.nama)
              return {
                value: k.id,
                label: `${k.nama} • [${info.code}] (${k.tahun_ajaran})`
              }
            })}
            required
          />

          <FormInput
            type="select"
            label="Hari KBM"
            name="hari"
            value={form.hari}
            onChange={(e) => setForm({ ...form, hari: e.target.value })}
            options={DAYS.map((d) => ({ value: d, label: d.toUpperCase() }))}
            required
          />

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
            <FormInput
              type="time"
              label="Jam Mulai"
              name="jam_mulai"
              value={form.jam_mulai}
              onChange={(e) => setForm({ ...form, jam_mulai: e.target.value })}
              required
            />
            <FormInput
              type="time"
              label="Jam Selesai"
              name="jam_selesai"
              value={form.jam_selesai}
              onChange={(e) => setForm({ ...form, jam_selesai: e.target.value })}
              required
            />
          </div>
        </form>
      </Modal>

      {/* Modal Hapus Jadwal */}
      <Modal
        isOpen={Boolean(itemToDelete)}
        onClose={() => setItemToDelete(null)}
        title="Konfirmasi Hapus Jadwal"
        size="sm"
        footer={
          <>
            <Button variant="secondary" onClick={() => setItemToDelete(null)} disabled={submitting}>
              Batal
            </Button>
            <Button variant="danger" onClick={handleDelete} loading={submitting}>
              Hapus Jadwal
            </Button>
          </>
        }
      >
        <p style={{ color: 'var(--text-light)', fontSize: '0.9rem' }}>
          Apakah Anda yakin ingin menghapus jadwal untuk mapel <strong>{itemToDelete?.mapel?.nama}</strong> pada hari <strong>{itemToDelete?.hari}</strong>?
        </p>
      </Modal>
    </div>
  )
}
