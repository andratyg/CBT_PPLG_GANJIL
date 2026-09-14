import React, { useState, useEffect, useMemo } from 'react'
import api from '../api'
import Card from '../components/Card'
import Table from '../components/Table'
import Button from '../components/Button'
import Modal from '../components/Modal'
import FormInput from '../components/FormInput'

export default function KelasPage({ user }) {
  const [kelasList, setKelasList] = useState([])
  const [allStudents, setAllStudents] = useState([])
  const [loading, setLoading] = useState(true)
  const [loadingStudents, setLoadingStudents] = useState(false)
  const [selectedKelas, setSelectedKelas] = useState(null)
  const [loadingDetail, setLoadingDetail] = useState(false)

  // Modals
  const [isAddClassModalOpen, setIsAddClassModalOpen] = useState(false)
  const [isAddStudentModalOpen, setIsAddStudentModalOpen] = useState(false)
  const [classToDelete, setClassToDelete] = useState(null)
  const [submitting, setSubmitting] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')
  const [successMsg, setSuccessMsg] = useState('')

  // Forms
  const [classForm, setClassForm] = useState({ nama: '', tahun_ajaran: '2026/2027' })
  const [addStudentTab, setAddStudentTab] = useState('bulk') // 'bulk' | 'checklist' | 'new'
  const [selectedStudentIds, setSelectedStudentIds] = useState([])
  const [studentSearchQuery, setStudentSearchQuery] = useState('')
  const [newStudentForm, setNewStudentForm] = useState({ name: '', email: '' })

  const isGuru = user?.role === 'guru'

  const loadKelas = () => {
    setLoading(true)
    api.get('/kelas')
      .then((res) => {
        setKelasList(res.data || [])
        if (selectedKelas) {
          viewDetail({ id: selectedKelas.id })
        }
      })
      .catch(console.error)
      .finally(() => setLoading(false))
  }

  const loadAllStudents = () => {
    setLoadingStudents(true)
    api.get('/siswa')
      .then((res) => {
        setAllStudents(res.data || [])
      })
      .catch(console.error)
      .finally(() => setLoadingStudents(false))
  }

  const viewDetail = async (k) => {
    setLoadingDetail(true)
    setSuccessMsg('')
    try {
      const res = await api.get(`/kelas/${k.id}`)
      setSelectedKelas(res.data)
    } catch (err) {
      console.error(err)
    } finally {
      setLoadingDetail(false)
    }
  }

  useEffect(() => {
    loadKelas()
    loadAllStudents()
  }, [])

  const handleCreateClass = async (e) => {
    e.preventDefault()
    setSubmitting(true)
    setErrorMsg('')
    try {
      await api.post('/kelas', classForm)
      setIsAddClassModalOpen(false)
      setClassForm({ nama: '', tahun_ajaran: '2026/2027' })
      loadKelas()
    } catch (err) {
      console.error(err)
      setErrorMsg(err.response?.data?.message || 'Gagal menambahkan kelas.')
    } finally {
      setSubmitting(false)
    }
  }

  const handleDeleteClass = async () => {
    if (!classToDelete) return
    setSubmitting(true)
    try {
      await api.delete(`/kelas/${classToDelete.id}`)
      setClassToDelete(null)
      if (selectedKelas?.id === classToDelete.id) {
        setSelectedKelas(null)
      }
      loadKelas()
    } catch (err) {
      console.error(err)
      alert('Gagal menghapus kelas.')
    } finally {
      setSubmitting(false)
    }
  }

  // Open Add Student Modal
  const openAddStudentModal = () => {
    setErrorMsg('')
    setSuccessMsg('')
    setStudentSearchQuery('')
    loadAllStudents()

    // Pre-select unassigned students by default
    const currentEnrolledIds = new Set((selectedKelas?.siswa || []).map((s) => s.id))
    const unassigned = allStudents.filter((s) => !currentEnrolledIds.has(s.id)).map((s) => s.id)
    setSelectedStudentIds(unassigned)
    setAddStudentTab(unassigned.length > 0 ? 'bulk' : 'checklist')
    setIsAddStudentModalOpen(true)
  }

  // Action 1: Add ALL students to class (Direct 1-Click)
  const handleQuickAddAllStudents = async () => {
    if (!selectedKelas) return
    setSubmitting(true)
    setErrorMsg('')
    setSuccessMsg('')
    try {
      const res = await api.post(`/kelas/${selectedKelas.id}/tambah-semua-siswa`)
      setSuccessMsg(res.data?.message || 'Seluruh siswa berhasil didaftarkan ke kelas!')
      setIsAddStudentModalOpen(false)
      viewDetail(selectedKelas)
      loadKelas()
    } catch (err) {
      console.error(err)
      setErrorMsg(err.response?.data?.message || 'Gagal menambahkan seluruh siswa ke kelas.')
    } finally {
      setSubmitting(false)
    }
  }

  // Action 2: Add selected students from checklist
  const handleAddSelectedStudents = async (e) => {
    if (e) e.preventDefault()
    if (!selectedKelas) return
    if (selectedStudentIds.length === 0) {
      setErrorMsg('Pilih minimal satu siswa dari daftar.')
      return
    }

    setSubmitting(true)
    setErrorMsg('')
    try {
      const res = await api.post(`/kelas/${selectedKelas.id}/siswa`, { siswa_ids: selectedStudentIds })
      setSuccessMsg(res.data?.message || `${selectedStudentIds.length} siswa berhasil didaftarkan ke kelas!`)
      setIsAddStudentModalOpen(false)
      viewDetail(selectedKelas)
      loadKelas()
    } catch (err) {
      console.error(err)
      setErrorMsg(err.response?.data?.message || 'Gagal mendaftarkan siswa terpilih.')
    } finally {
      setSubmitting(false)
    }
  }

  // Action 3: Create a new student and enroll directly
  const handleCreateNewStudent = async (e) => {
    e.preventDefault()
    if (!selectedKelas) return
    setSubmitting(true)
    setErrorMsg('')
    try {
      const res = await api.post(`/kelas/${selectedKelas.id}/daftar-siswa-baru`, newStudentForm)
      setSuccessMsg(res.data?.message || 'Siswa baru berhasil dibuat dan didaftarkan ke kelas!')
      setNewStudentForm({ name: '', email: '' })
      setIsAddStudentModalOpen(false)
      viewDetail(selectedKelas)
      loadKelas()
      loadAllStudents()
    } catch (err) {
      console.error(err)
      setErrorMsg(err.response?.data?.message || 'Gagal mendaftarkan siswa baru. Pastikan email belum digunakan.')
    } finally {
      setSubmitting(false)
    }
  }

  const handleRemoveStudent = async (siswaId) => {
    if (!selectedKelas || !confirm('Keluarkan siswa ini dari kelas?')) return
    try {
      await api.delete(`/kelas/${selectedKelas.id}/siswa/${siswaId}`)
      viewDetail(selectedKelas)
      loadKelas()
    } catch (err) {
      console.error(err)
      alert('Gagal mengeluarkan siswa.')
    }
  }

  // Filtered Students for Checklist
  const enrolledStudentIds = useMemo(() => {
    return new Set((selectedKelas?.siswa || []).map((s) => s.id))
  }, [selectedKelas])

  const filteredStudentList = useMemo(() => {
    return allStudents.filter((s) => {
      if (!studentSearchQuery.trim()) return true
      const q = studentSearchQuery.toLowerCase()
      return s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q)
    })
  }, [allStudents, studentSearchQuery])

  const unassignedCount = useMemo(() => {
    return allStudents.filter((s) => !enrolledStudentIds.has(s.id)).length
  }, [allStudents, enrolledStudentIds])

  const studentColumns = [
    {
      header: 'No',
      key: 'no',
      width: '50px',
      render: (_, __, idx) => <span className="text-muted">{idx + 1}</span>
    },
    {
      header: 'Nama Lengkap Siswa',
      key: 'name',
      render: (s) => (
        <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
          <div
            style={{
              width: '32px',
              height: '32px',
              borderRadius: '50%',
              background: 'var(--accent-light, rgba(16, 185, 129, 0.15))',
              color: 'var(--accent)',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              fontSize: '0.8rem',
              fontWeight: 800
            }}
          >
            {s.name?.charAt(0) || 'S'}
          </div>
          <div>
            <strong style={{ display: 'block', fontSize: '0.9rem' }}>{s.name}</strong>
            <span style={{ fontSize: '0.75rem', color: 'var(--muted)' }}>Akun Siswa</span>
          </div>
        </div>
      )
    },
    {
      header: 'Email Akun',
      key: 'email',
      render: (s) => <span className="text-muted" style={{ fontSize: '0.85rem' }}>{s.email}</span>
    },
    ...(isGuru
      ? [
          {
            header: 'Aksi',
            key: 'aksi',
            width: '110px',
            align: 'right',
            render: (s) => (
              <Button
                size="sm"
                variant="ghost"
                onClick={() => handleRemoveStudent(s.id)}
                title="Keluarkan dari kelas"
                style={{ color: 'var(--danger)', fontSize: '0.775rem' }}
              >
                ❌ Keluarkan
              </Button>
            )
          }
        ]
      : [])
  ]

  return (
    <div style={{ maxWidth: 1280, margin: '0 auto' }}>
      {/* Page Header */}
      <div className="page-header">
        <div>
          <h1 className="page-title">
            <span>Manajemen Rombongan Belajar (Kelas)</span>
            <span className="badge badge-neutral">{kelasList.length} Rombel</span>
          </h1>
          <p className="page-subtitle">Daftar rombongan belajar seluruh program keahlian (PPLG, TKJ, DKV, MPLB) beserta data siswa terdaftar</p>
        </div>
        {isGuru && (
          <Button variant="primary" icon="➕" onClick={() => setIsAddClassModalOpen(true)}>
            Tambah Kelas Baru
          </Button>
        )}
      </div>

      {/* Grid Kelas Cards */}
      <div className="grid grid-3 mb-6" style={{ gap: '1.25rem' }}>
        {loading ? (
          <div className="card" style={{ gridColumn: '1 / -1', textAlign: 'center', padding: '2.5rem' }}>
            <span className="text-muted">Memuat daftar kelas...</span>
          </div>
        ) : kelasList.length === 0 ? (
          <div className="card" style={{ gridColumn: '1 / -1', textAlign: 'center', padding: '2.5rem' }}>
            <div className="empty-state-icon">🏫</div>
            <div className="empty-state-title">Belum ada kelas terdaftar</div>
            <p className="text-muted" style={{ fontSize: '0.85rem' }}>Klik tombol "Tambah Kelas Baru" untuk memulai.</p>
          </div>
        ) : (
          kelasList.map((k) => {
            const isSelected = selectedKelas?.id === k.id
            const upper = (k.nama || '').toUpperCase()
            const code = upper.includes('PPLG') ? 'PPLG' : (upper.includes('TKJ') ? 'TKJ' : (upper.includes('DKV') ? 'DKV' : (upper.includes('MPLB') ? 'MPLB' : 'SMK')))

            return (
              <div
                key={k.id}
                className="card card-hover"
                style={{
                  cursor: 'pointer',
                  borderColor: isSelected ? 'var(--accent)' : 'var(--border)',
                  boxShadow: isSelected ? '0 0 0 2px rgba(16, 185, 129, 0.25)' : 'var(--shadow-sm)',
                  transition: 'all 0.15s ease'
                }}
                onClick={() => viewDetail(k)}
              >
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '0.75rem' }}>
                  <span className={`badge badge-${code.toLowerCase()}`}>{code}</span>
                  {isGuru && (
                    <button
                      type="button"
                      style={{ background: 'transparent', border: 'none', color: 'var(--muted)', cursor: 'pointer', padding: '0.2rem' }}
                      onClick={(e) => {
                        e.stopPropagation()
                        setClassToDelete(k)
                      }}
                      title="Hapus Kelas"
                    >
                      🗑️
                    </button>
                  )}
                </div>

                <h3 style={{ fontSize: '1.25rem', fontWeight: 800, color: 'var(--text)', marginBottom: '0.25rem' }}>
                  {k.nama}
                </h3>
                <div className="text-muted" style={{ fontSize: '0.825rem', marginBottom: '1rem' }}>
                  Tahun Ajaran: {k.tahun_ajaran}
                </div>

                <div
                  style={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between',
                    paddingTop: '0.75rem',
                    borderTop: '1px solid var(--border)',
                    fontSize: '0.85rem'
                  }}
                >
                  <span style={{ display: 'flex', alignItems: 'center', gap: '0.4rem', color: 'var(--muted)' }}>
                    👥 <strong>{k.siswa_count ?? 0}</strong> Siswa Terdaftar
                  </span>
                  <span style={{ color: isSelected ? 'var(--accent)' : 'var(--muted)', fontWeight: 700, fontSize: '0.8rem' }}>
                    {isSelected ? '✓ Terpilih' : 'Kelola Siswa →'}
                  </span>
                </div>
              </div>
            )
          })
        )}
      </div>

      {/* Detail Siswa Panel */}
      {selectedKelas && (
        <Card
          title={`Daftar Siswa — ${selectedKelas.nama}`}
          subtitle={`Tahun Ajaran ${selectedKelas.tahun_ajaran} • Total ${selectedKelas.siswa?.length || 0} Siswa Terdaftar`}
          action={
            isGuru && (
              <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', flexWrap: 'wrap' }}>
                <Button
                  size="sm"
                  variant="primary"
                  icon="⚡"
                  onClick={handleQuickAddAllStudents}
                  loading={submitting}
                  title="Daftarkan seluruh siswa di sistem ke kelas ini sekaligus"
                >
                  Tambah Seluruh Siswa ({unassignedCount})
                </Button>
                <Button
                  size="sm"
                  variant="outline"
                  icon="➕"
                  onClick={openAddStudentModal}
                >
                  Pilih Siswa...
                </Button>
              </div>
            )
          }
        >
          {successMsg && (
            <div style={{ background: 'var(--accent-light)', border: '1px solid var(--accent-border)', color: 'var(--accent)', padding: '0.85rem 1.25rem', borderRadius: 'var(--radius-md)', marginBottom: '1.25rem', fontWeight: 600, display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
              <span>✅</span>
              <span>{successMsg}</span>
            </div>
          )}

          {loadingDetail ? (
            <div style={{ textAlign: 'center', padding: '2rem', color: 'var(--muted)' }}>
              Memuat data siswa kelas...
            </div>
          ) : !selectedKelas.siswa || selectedKelas.siswa.length === 0 ? (
            <div className="empty-state" style={{ padding: '3rem 1.5rem', textAlign: 'center' }}>
              <div className="empty-state-icon" style={{ fontSize: '3rem', marginBottom: '0.75rem' }}>👥</div>
              <div className="empty-state-title" style={{ fontSize: '1.15rem', fontWeight: 800 }}>
                Belum ada siswa terdaftar di kelas {selectedKelas.nama}
              </div>
              <p className="text-muted" style={{ fontSize: '0.875rem', maxWidth: 450, margin: '0.5rem auto 1.5rem' }}>
                Anda dapat menambahkan seluruh siswa yang ada di sistem sekaligus atau memilih siswa tertentu.
              </p>
              <div style={{ display: 'flex', gap: '0.75rem', justifyContent: 'center' }}>
                <Button
                  variant="primary"
                  icon="⚡"
                  onClick={handleQuickAddAllStudents}
                  loading={submitting}
                >
                  Tambahkan Seluruh Siswa Sekaligus ({allStudents.length} Siswa)
                </Button>
                <Button
                  variant="outline"
                  icon="➕"
                  onClick={openAddStudentModal}
                >
                  Pilih Siswa Manual
                </Button>
              </div>
            </div>
          ) : (
            <Table
              columns={studentColumns}
              data={selectedKelas.siswa}
              emptyMessage="Tidak ada siswa terdaftar."
            />
          )}
        </Card>
      )}

      {/* Modal Tambah Kelas Baru */}
      <Modal
        isOpen={isAddClassModalOpen}
        onClose={() => setIsAddClassModalOpen(false)}
        title="Tambah Rombongan Belajar Baru"
        footer={
          <>
            <Button variant="secondary" onClick={() => setIsAddClassModalOpen(false)} disabled={submitting}>
              Batal
            </Button>
            <Button variant="primary" onClick={handleCreateClass} loading={submitting}>
              Simpan Kelas
            </Button>
          </>
        }
      >
        <form onSubmit={handleCreateClass}>
          {errorMsg && (
            <div className="badge badge-danger mb-4" style={{ width: '100%', padding: '0.6rem' }}>
              ⚠️ {errorMsg}
            </div>
          )}
          <FormInput
            label="Nama Kelas"
            name="nama"
            value={classForm.nama}
            onChange={(e) => setClassForm({ ...classForm, nama: e.target.value })}
            placeholder="Contoh: XII PPLG 1 / XII TKJ 1 / XI DKV 1 / X MPLB 1"
            required
          />
          <FormInput
            label="Tahun Ajaran"
            name="tahun_ajaran"
            value={classForm.tahun_ajaran}
            onChange={(e) => setClassForm({ ...classForm, tahun_ajaran: e.target.value })}
            placeholder="2026/2027"
            required
          />
        </form>
      </Modal>

      {/* =========================================================================
          MODAL TAMBAH SISWA KE KELAS (DENGAN 3 TAB: BULK, CHECKLIST, SISWA BARU)
          ========================================================================= */}
      <Modal
        isOpen={isAddStudentModalOpen}
        onClose={() => setIsAddStudentModalOpen(false)}
        title={`Tambah Siswa ke ${selectedKelas?.nama}`}
        size="md"
        footer={
          addStudentTab === 'checklist' ? (
            <>
              <Button variant="secondary" onClick={() => setIsAddStudentModalOpen(false)} disabled={submitting}>
                Batal
              </Button>
              <Button
                variant="primary"
                onClick={handleAddSelectedStudents}
                loading={submitting}
                disabled={selectedStudentIds.length === 0}
              >
                Daftarkan {selectedStudentIds.length} Siswa Terpilih
              </Button>
            </>
          ) : addStudentTab === 'new' ? (
            <>
              <Button variant="secondary" onClick={() => setIsAddStudentModalOpen(false)} disabled={submitting}>
                Batal
              </Button>
              <Button variant="primary" onClick={handleCreateNewStudent} loading={submitting}>
                Simpan & Daftarkan Siswa
              </Button>
            </>
          ) : (
            <Button variant="secondary" onClick={() => setIsAddStudentModalOpen(false)}>
              Tutup
            </Button>
          )
        }
      >
        <div>
          {errorMsg && (
            <div className="badge badge-danger mb-4" style={{ width: '100%', padding: '0.75rem', fontSize: '0.825rem' }}>
              ⚠️ {errorMsg}
            </div>
          )}

          {/* Tab Navigators */}
          <div style={{ display: 'flex', gap: '0.4rem', borderBottom: '1px solid var(--border)', paddingBottom: '0.75rem', marginBottom: '1.25rem' }}>
            <button
              type="button"
              className={`btn btn-sm ${addStudentTab === 'bulk' ? 'btn-primary' : 'btn-secondary'}`}
              onClick={() => setAddStudentTab('bulk')}
              style={{ borderRadius: 'var(--radius-full)', fontSize: '0.8rem', display: 'inline-flex', alignItems: 'center', gap: '0.35rem' }}
            >
              <span>⚡ Tambahkan Seluruhnya</span>
            </button>
            <button
              type="button"
              className={`btn btn-sm ${addStudentTab === 'checklist' ? 'btn-primary' : 'btn-secondary'}`}
              onClick={() => setAddStudentTab('checklist')}
              style={{ borderRadius: 'var(--radius-full)', fontSize: '0.8rem', display: 'inline-flex', alignItems: 'center', gap: '0.35rem' }}
            >
              <span>☑️ Pilih dari Daftar ({allStudents.length})</span>
            </button>
            <button
              type="button"
              className={`btn btn-sm ${addStudentTab === 'new' ? 'btn-primary' : 'btn-secondary'}`}
              onClick={() => setAddStudentTab('new')}
              style={{ borderRadius: 'var(--radius-full)', fontSize: '0.8rem', display: 'inline-flex', alignItems: 'center', gap: '0.35rem' }}
            >
              <span>➕ Buat Siswa Baru</span>
            </button>
          </div>

          {/* TAB 1: BULK ADD ALL STUDENTS */}
          {addStudentTab === 'bulk' && (
            <div style={{ textAlign: 'center', padding: '1rem 0.5rem' }}>
              <div
                style={{
                  width: '64px',
                  height: '64px',
                  background: 'rgba(16, 185, 129, 0.12)',
                  color: '#059669',
                  borderRadius: '50%',
                  display: 'inline-flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  fontSize: '2rem',
                  marginBottom: '1rem'
                }}
              >
                ⚡
              </div>
              <h3 style={{ fontSize: '1.2rem', fontWeight: 800, color: 'var(--text)', marginBottom: '0.5rem' }}>
                Daftarkan Seluruh Siswa Sekaligus
              </h3>
              <p style={{ color: 'var(--muted)', fontSize: '0.875rem', lineHeight: 1.5, maxWidth: 420, margin: '0 auto 1.5rem' }}>
                Sistem akan secara otomatis menghubungkan seluruh <strong>{allStudents.length} siswa</strong> yang terdaftar di basis data ke dalam kelas <strong>{selectedKelas?.nama}</strong>.
              </p>

              <div
                style={{
                  display: 'grid',
                  gridTemplateColumns: '1fr 1fr',
                  gap: '0.75rem',
                  background: 'var(--surface-alt)',
                  border: '1px solid var(--border)',
                  padding: '1rem',
                  borderRadius: 'var(--radius-md)',
                  marginBottom: '1.5rem',
                  textAlign: 'left'
                }}
              >
                <div>
                  <div style={{ fontSize: '0.75rem', color: 'var(--muted)' }}>Total Siswa Terdaftar:</div>
                  <div style={{ fontSize: '1.25rem', fontWeight: 800, color: 'var(--text)' }}>{allStudents.length} Siswa</div>
                </div>
                <div>
                  <div style={{ fontSize: '0.75rem', color: 'var(--muted)' }}>Sudah di Kelas Ini:</div>
                  <div style={{ fontSize: '1.25rem', fontWeight: 800, color: 'var(--accent)' }}>{selectedKelas?.siswa?.length || 0} Siswa</div>
                </div>
              </div>

              <Button
                variant="primary"
                size="lg"
                icon="⚡"
                style={{ width: '100%', padding: '0.85rem' }}
                onClick={handleQuickAddAllStudents}
                loading={submitting}
              >
                Ya, Tambahkan Seluruh Siswa Sekarang ({allStudents.length} Siswa)
              </Button>
            </div>
          )}

          {/* TAB 2: MULTI-SELECT CHECKLIST */}
          {addStudentTab === 'checklist' && (
            <div>
              {/* Search & Bulk Select Actions */}
              <div style={{ marginBottom: '1rem' }}>
                <input
                  type="text"
                  className="form-control"
                  placeholder="🔍 Cari nama siswa atau email..."
                  value={studentSearchQuery}
                  onChange={(e) => setStudentSearchQuery(e.target.value)}
                  style={{ width: '100%', padding: '0.55rem 0.85rem', fontSize: '0.85rem', borderRadius: 'var(--radius-md)', marginBottom: '0.65rem' }}
                />

                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '0.5rem', fontSize: '0.8rem' }}>
                  <div style={{ display: 'flex', gap: '0.4rem' }}>
                    <button
                      type="button"
                      className="btn btn-sm btn-secondary"
                      onClick={() => setSelectedStudentIds(allStudents.map((s) => s.id))}
                      style={{ fontSize: '0.725rem', padding: '0.2rem 0.5rem' }}
                    >
                      ✓ Pilih Semua ({allStudents.length})
                    </button>
                    <button
                      type="button"
                      className="btn btn-sm btn-secondary"
                      onClick={() => {
                        const unassigned = allStudents.filter((s) => !enrolledStudentIds.has(s.id)).map((s) => s.id)
                        setSelectedStudentIds(unassigned)
                      }}
                      style={{ fontSize: '0.725rem', padding: '0.2rem 0.5rem' }}
                    >
                      Pilih yang Belum Terdaftar ({unassignedCount})
                    </button>
                    <button
                      type="button"
                      className="btn btn-sm btn-ghost"
                      onClick={() => setSelectedStudentIds([])}
                      style={{ fontSize: '0.725rem', padding: '0.2rem 0.5rem', color: 'var(--danger)' }}
                    >
                      Batal Pilih
                    </button>
                  </div>
                  <span style={{ fontWeight: 700, color: 'var(--accent)' }}>
                    {selectedStudentIds.length} Siswa Terpilih
                  </span>
                </div>
              </div>

              {/* Scrollable Student List */}
              <div
                style={{
                  maxHeight: '320px',
                  overflowY: 'auto',
                  border: '1px solid var(--border)',
                  borderRadius: 'var(--radius-md)',
                  background: 'var(--card)'
                }}
              >
                {loadingStudents ? (
                  <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--muted)', fontSize: '0.85rem' }}>
                    Memuat daftar siswa...
                  </div>
                ) : filteredStudentList.length === 0 ? (
                  <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--muted)', fontSize: '0.85rem' }}>
                    Tidak ada siswa yang sesuai pencarian.
                  </div>
                ) : (
                  filteredStudentList.map((s) => {
                    const isEnrolled = enrolledStudentIds.has(s.id)
                    const isChecked = selectedStudentIds.includes(s.id)

                    return (
                      <div
                        key={s.id}
                        onClick={() => {
                          if (isChecked) {
                            setSelectedStudentIds(selectedStudentIds.filter((id) => id !== s.id))
                          } else {
                            setSelectedStudentIds([...selectedStudentIds, s.id])
                          }
                        }}
                        style={{
                          display: 'flex',
                          alignItems: 'center',
                          justifyContent: 'space-between',
                          padding: '0.65rem 0.85rem',
                          borderBottom: '1px solid var(--border)',
                          cursor: 'pointer',
                          background: isChecked ? 'var(--accent-light, rgba(16, 185, 129, 0.08))' : 'transparent',
                          transition: 'background 0.1s ease'
                        }}
                      >
                        <div style={{ display: 'flex', alignItems: 'center', gap: '0.65rem' }}>
                          <input
                            type="checkbox"
                            checked={isChecked}
                            onChange={() => {}} // Handled by container onClick
                            style={{ cursor: 'pointer', transform: 'scale(1.15)' }}
                          />
                          <div>
                            <strong style={{ fontSize: '0.875rem', color: 'var(--text)' }}>{s.name}</strong>
                            <div style={{ fontSize: '0.75rem', color: 'var(--muted)' }}>{s.email}</div>
                          </div>
                        </div>

                        <div>
                          {isEnrolled ? (
                            <span className="badge badge-accent" style={{ fontSize: '0.65rem' }}>
                              ✓ Sudah di Kelas
                            </span>
                          ) : (
                            <span className="badge badge-neutral" style={{ fontSize: '0.65rem' }}>
                              + Belum Terdaftar
                            </span>
                          )}
                        </div>
                      </div>
                    )
                  })
                )}
              </div>
            </div>
          )}

          {/* TAB 3: CREATE NEW STUDENT */}
          {addStudentTab === 'new' && (
            <form onSubmit={handleCreateNewStudent}>
              <p style={{ color: 'var(--muted)', fontSize: '0.85rem', marginBottom: '1rem' }}>
                Buat akun siswa baru langsung dari halaman ini. Siswa akan otomatis didaftarkan ke kelas <strong>{selectedKelas?.nama}</strong>.
              </p>

              <FormInput
                label="Nama Lengkap Siswa"
                name="name"
                value={newStudentForm.name}
                onChange={(e) => setNewStudentForm({ ...newStudentForm, name: e.target.value })}
                placeholder="Contoh: Muhammad Kevin Al-Farabi"
                required
              />

              <FormInput
                type="email"
                label="Email Siswa"
                name="email"
                value={newStudentForm.email}
                onChange={(e) => setNewStudentForm({ ...newStudentForm, email: e.target.value })}
                placeholder="kevin@cbt.test"
                required
              />
            </form>
          )}
        </div>
      </Modal>

      {/* Modal Hapus Kelas */}
      <Modal
        isOpen={Boolean(classToDelete)}
        onClose={() => setClassToDelete(null)}
        title="Konfirmasi Hapus Kelas"
        size="sm"
        footer={
          <>
            <Button variant="secondary" onClick={() => setClassToDelete(null)} disabled={submitting}>
              Batal
            </Button>
            <Button variant="danger" onClick={handleDeleteClass} loading={submitting}>
              Hapus Kelas
            </Button>
          </>
        }
      >
        <p style={{ color: 'var(--text-light)', fontSize: '0.9rem' }}>
          Apakah Anda yakin ingin menghapus kelas <strong>{classToDelete?.nama}</strong>? Seluruh data jadwal dan riwayat kelas ini akan terdampak.
        </p>
      </Modal>
    </div>
  )
}
