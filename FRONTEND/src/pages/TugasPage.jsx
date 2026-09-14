import React, { useState, useEffect } from 'react'
import api from '../api'
import Card from '../components/Card'
import Button from '../components/Button'
import Modal from '../components/Modal'
import FormInput from '../components/FormInput'

const STORAGE_URL = import.meta.env.VITE_STORAGE_URL || 'http://localhost:8000/storage/'

export default function TugasPage({ user }) {
  const [pertemuanList, setPertemuanList] = useState([])
  const [selectedPertemuanId, setSelectedPertemuanId] = useState('')
  const [tugasList, setTugasList] = useState([])
  const [loading, setLoading] = useState(true)

  // Modals
  const [isAddModalOpen, setIsAddModalOpen] = useState(false)
  const [isSubmitModalOpen, setIsSubmitModalOpen] = useState(false)
  const [isGradingModalOpen, setIsGradingModalOpen] = useState(false)
  const [selectedTugas, setSelectedTugas] = useState(null)
  const [detailTugas, setDetailTugas] = useState(null)
  const [loadingDetail, setLoadingDetail] = useState(false)

  const [submitting, setSubmitting] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

  // Create Task Form (Guru)
  const [taskForm, setTaskForm] = useState({
    judul: '',
    deskripsi: '',
    deadline: '',
    nilai_maksimal: 100
  })

  // Submit Task Form (Siswa)
  const [submitForm, setSubmitForm] = useState({
    file: null,
    catatan: ''
  })

  // Grading Form (Guru)
  const [gradingScores, setGradingScores] = useState({})
  const [gradingFeedbacks, setGradingFeedbacks] = useState({})

  const isGuru = user?.role === 'guru'

  const loadTugas = (pertemuanId) => {
    if (!pertemuanId) return
    setLoading(true)
    api.get(`/pertemuan/${pertemuanId}/tugas`)
      .then((res) => setTugasList(res.data || []))
      .catch(console.error)
      .finally(() => setLoading(false))
  }

  useEffect(() => {
    api.get('/pertemuan')
      .then((res) => {
        const list = res.data || []
        setPertemuanList(list)
        if (list.length > 0) {
          setSelectedPertemuanId(list[0].id)
          loadTugas(list[0].id)
        } else {
          setLoading(false)
        }
      })
      .catch((err) => {
        console.error(err)
        setLoading(false)
      })
  }, [])

  const handleSelectPertemuan = (id) => {
    setSelectedPertemuanId(id)
    loadTugas(id)
  }

  // Create Task
  const handleCreateTask = async (e) => {
    e.preventDefault()
    if (!selectedPertemuanId) return
    setSubmitting(true)
    setErrorMsg('')
    try {
      await api.post(`/pertemuan/${selectedPertemuanId}/tugas`, {
        ...taskForm,
        nilai_maksimal: Number(taskForm.nilai_maksimal)
      })
      setIsAddModalOpen(false)
      setTaskForm({ judul: '', deskripsi: '', deadline: '', nilai_maksimal: 100 })
      loadTugas(selectedPertemuanId)
    } catch (err) {
      console.error(err)
      setErrorMsg(err.response?.data?.message || 'Gagal membuat tugas.')
    } finally {
      setSubmitting(false)
    }
  }

  // Submit Task (Siswa)
  const handleSubmitTask = async (e) => {
    e.preventDefault()
    if (!selectedTugas || !submitForm.file) return
    setSubmitting(true)
    setErrorMsg('')
    try {
      const formData = new FormData()
      formData.append('file', submitForm.file)
      if (submitForm.catatan) formData.append('catatan', submitForm.catatan)

      await api.post(`/tugas/${selectedTugas.id}/kumpulkan`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })

      alert('Tugas berhasil dikumpulkan!')
      setIsSubmitModalOpen(false)
      setSubmitForm({ file: null, catatan: '' })
      loadTugas(selectedPertemuanId)
    } catch (err) {
      console.error(err)
      setErrorMsg(err.response?.data?.message || 'Gagal mengumpulkan tugas.')
    } finally {
      setSubmitting(false)
    }
  }

  // Open Grading Drawer (Guru)
  const openGradingModal = async (tugas) => {
    setSelectedTugas(tugas)
    setIsGradingModalOpen(true)
    setLoadingDetail(true)
    try {
      const res = await api.get(`/tugas/${tugas.id}`)
      setDetailTugas(res.data)
      const scores = {}
      const fbs = {}
      res.data?.pengumpulan?.forEach((p) => {
        scores[p.id] = p.nilai ?? ''
        fbs[p.id] = p.feedback ?? ''
      })
      setGradingScores(scores)
      setGradingFeedbacks(fbs)
    } catch (err) {
      console.error(err)
    } finally {
      setLoadingDetail(false)
    }
  }

  // Save Score
  const handleSaveScore = async (pengumpulanId) => {
    const val = Number(gradingScores[pengumpulanId])
    if (isNaN(val) || val < 0 || val > 100) {
      alert('Nilai harus berupa angka antara 0 dan 100.')
      return
    }
    try {
      await api.put(`/pengumpulan/${pengumpulanId}/nilai`, {
        nilai: val,
        feedback: gradingFeedbacks[pengumpulanId] || ''
      })
      alert('Nilai tersimpan!')
    } catch (err) {
      console.error(err)
      alert('Gagal menyimpan nilai.')
    }
  }

  const handleDeleteTask = async (tugasId) => {
    if (!confirm('Hapus penugasan ini?')) return
    try {
      await api.delete(`/tugas/${tugasId}`)
      loadTugas(selectedPertemuanId)
    } catch (err) {
      console.error(err)
      alert('Gagal menghapus tugas.')
    }
  }

  return (
    <div>
      <div className="page-header">
        <div>
          <h1 className="page-title">
            <span>Penugasan & Evaluasi</span>
            <span className="badge badge-neutral">{tugasList.length} Tugas</span>
          </h1>
          <p className="page-subtitle">Pemberian tugas praktik, deadline pengumpulan, dan penilaian siswa</p>
        </div>
        {isGuru && (
          <Button
            variant="primary"
            icon="➕"
            onClick={() => {
              // Default deadline to tomorrow
              const d = new Date()
              d.setDate(d.getDate() + 7)
              setTaskForm({
                judul: '',
                deskripsi: '',
                deadline: d.toISOString().slice(0, 16),
                nilai_maksimal: 100
              })
              setErrorMsg('')
              setIsAddModalOpen(true)
            }}
          >
            Buat Tugas Baru
          </Button>
        )}
      </div>

      {/* Filter Pertemuan Dropdown */}
      <Card className="mb-6">
        <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', flexWrap: 'wrap' }}>
          <label style={{ fontWeight: 600, fontSize: '0.875rem' }}>Pilih Sesi Pertemuan:</label>
          <select
            className="form-select"
            style={{ maxWidth: '450px' }}
            value={selectedPertemuanId}
            onChange={(e) => handleSelectPertemuan(e.target.value)}
          >
            {pertemuanList.length === 0 && <option value="">Belum ada pertemuan terdaftar</option>}
            {pertemuanList.map((p) => (
              <option key={p.id} value={p.id}>
                Pertemuan {p.pertemuan_ke}: {p.topik} ({p.tanggal})
              </option>
            ))}
          </select>
        </div>
      </Card>

      {/* Task List */}
      <div className="grid grid-2">
        {loading ? (
          <div className="card" style={{ gridColumn: '1 / -1', textAlign: 'center', padding: '2.5rem' }}>
            <span className="text-muted">Memuat daftar penugasan...</span>
          </div>
        ) : tugasList.length === 0 ? (
          <div className="card" style={{ gridColumn: '1 / -1', textAlign: 'center', padding: '2.5rem' }}>
            <div className="empty-state-icon">📝</div>
            <div className="empty-state-title">Belum ada tugas pada sesi ini</div>
            <p className="text-muted" style={{ fontSize: '0.825rem' }}>
              {isGuru ? 'Klik "Buat Tugas Baru" untuk menambahkan latihan / penugasan siswa.' : 'Tidak ada tugas yang harus dikumpulkan.'}
            </p>
          </div>
        ) : (
          tugasList.map((t) => {
            const isDeadlinePassed = new Date(t.deadline) < new Date()
            return (
              <div key={t.id} className="card card-hover">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '0.75rem' }}>
                  <span className={`badge ${isDeadlinePassed ? 'badge-danger' : 'badge-accent'}`}>
                    {isDeadlinePassed ? '⚠️ Deadline Lewat' : '⏳ Aktif'}
                  </span>
                  <span className="badge badge-neutral">Max: {t.nilai_maksimal} Poin</span>
                </div>

                <h3 style={{ fontSize: '1.2rem', fontWeight: 700, color: 'var(--text)', marginBottom: '0.4rem' }}>
                  {t.judul}
                </h3>

                <p style={{ fontSize: '0.85rem', color: 'var(--text-light)', marginBottom: '1rem', whiteSpace: 'pre-line' }}>
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
                  📅 <strong>Batas Pengumpulan:</strong> {t.deadline}
                </div>

                <div
                  style={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between',
                    paddingTop: '0.75rem',
                    borderTop: '1px solid var(--border)'
                  }}
                >
                  <span style={{ fontSize: '0.825rem', color: 'var(--muted)' }}>
                    📥 <strong>{t.pengumpulan_count ?? 0}</strong> Terkumpul
                  </span>

                  <div style={{ display: 'flex', gap: '0.4rem' }}>
                    {isGuru ? (
                      <>
                        <Button
                          size="sm"
                          variant="outline"
                          onClick={() => openGradingModal(t)}
                        >
                          👁️ Periksa & Nilai
                        </Button>
                        <Button
                          size="sm"
                          variant="danger"
                          onClick={() => handleDeleteTask(t.id)}
                          title="Hapus Tugas"
                        >
                          🗑️
                        </Button>
                      </>
                    ) : (
                      <Button
                        size="sm"
                        variant="primary"
                        onClick={() => {
                          setSelectedTugas(t)
                          setIsSubmitModalOpen(true)
                        }}
                      >
                        📤 Kumpulkan Tugas
                      </Button>
                    )}
                  </div>
                </div>
              </div>
            )
          })
        )}
      </div>

      {/* Modal Tambah Tugas (Guru) */}
      <Modal
        isOpen={isAddModalOpen}
        onClose={() => setIsAddModalOpen(false)}
        title="Buat Penugasan Baru"
        footer={
          <>
            <Button variant="secondary" onClick={() => setIsAddModalOpen(false)} disabled={submitting}>
              Batal
            </Button>
            <Button variant="primary" onClick={handleCreateTask} loading={submitting}>
              Terbitkan Tugas
            </Button>
          </>
        }
      >
        <form onSubmit={handleCreateTask}>
          {errorMsg && (
            <div className="badge badge-danger mb-4" style={{ width: '100%', padding: '0.6rem' }}>
              ⚠️ {errorMsg}
            </div>
          )}

          <FormInput
            label="Judul Tugas"
            name="judul"
            value={taskForm.judul}
            onChange={(e) => setTaskForm({ ...taskForm, judul: e.target.value })}
            placeholder="Contoh: Tugas 01 - Pembuatan RESTful API Kelas PPLG"
            required
          />

          <FormInput
            type="textarea"
            label="Deskripsi & Petunjuk Pengerjaan"
            name="deskripsi"
            value={taskForm.deskripsi}
            onChange={(e) => setTaskForm({ ...taskForm, deskripsi: e.target.value })}
            placeholder="Jelaskan kriteria penilaian dan format file yang harus dikirimkan..."
            rows={4}
            required
          />

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
            <FormInput
              type="datetime-local"
              label="Batas Waktu (Deadline)"
              name="deadline"
              value={taskForm.deadline}
              onChange={(e) => setTaskForm({ ...taskForm, deadline: e.target.value })}
              required
            />

            <FormInput
              type="number"
              label="Nilai Maksimal"
              name="nilai_maksimal"
              min={1}
              max={100}
              value={taskForm.nilai_maksimal}
              onChange={(e) => setTaskForm({ ...taskForm, nilai_maksimal: e.target.value })}
              required
            />
          </div>
        </form>
      </Modal>

      {/* Modal Kumpulkan Tugas (Siswa) */}
      <Modal
        isOpen={isSubmitModalOpen}
        onClose={() => setIsSubmitModalOpen(false)}
        title={`Kumpulkan Tugas: ${selectedTugas?.judul}`}
        footer={
          <>
            <Button variant="secondary" onClick={() => setIsSubmitModalOpen(false)} disabled={submitting}>
              Batal
            </Button>
            <Button variant="primary" onClick={handleSubmitTask} loading={submitting}>
              Kirim Tugas
            </Button>
          </>
        }
      >
        <form onSubmit={handleSubmitTask}>
          {errorMsg && (
            <div className="badge badge-danger mb-4" style={{ width: '100%', padding: '0.6rem' }}>
              ⚠️ {errorMsg}
            </div>
          )}

          <FormInput
            type="file"
            label="Lampirkan File Jawaban (ZIP, PDF, DOCX)"
            name="file"
            onChange={(e) => setSubmitForm({ ...submitForm, file: e.target.files[0] })}
            helperText="Ukuran maksimal 50 MB"
            required
          />

          <FormInput
            type="textarea"
            label="Catatan Pengumpulan (Opsional)"
            name="catatan"
            value={submitForm.catatan}
            onChange={(e) => setSubmitForm({ ...submitForm, catatan: e.target.value })}
            placeholder="Tuliskan catatan pengerjaan atau link repository github jika ada..."
            rows={3}
          />
        </form>
      </Modal>

      {/* Modal Pemeriksaan & Penilaian Guru */}
      <Modal
        isOpen={isGradingModalOpen}
        onClose={() => setIsGradingModalOpen(false)}
        title={`Penilaian Tugas: ${selectedTugas?.judul}`}
        size="lg"
        footer={
          <Button variant="secondary" onClick={() => setIsGradingModalOpen(false)}>
            Tutup
          </Button>
        }
      >
        {loadingDetail ? (
          <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--muted)' }}>
            Memuat berkas pengumpulan siswa...
          </div>
        ) : !detailTugas?.pengumpulan || detailTugas.pengumpulan.length === 0 ? (
          <div className="empty-state" style={{ padding: '2rem' }}>
            <div className="empty-state-icon">📭</div>
            <div className="empty-state-title">Belum ada siswa yang mengumpulkan tugas ini</div>
          </div>
        ) : (
          <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
            {detailTugas.pengumpulan.map((p) => (
              <div
                key={p.id}
                style={{
                  background: 'var(--card-subtle)',
                  border: '1px solid var(--border)',
                  borderRadius: 'var(--radius-md)',
                  padding: '1rem'
                }}
              >
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '0.5rem' }}>
                  <div>
                    <strong>{p.siswa?.name}</strong>
                    <span className="text-muted" style={{ fontSize: '0.8rem', marginLeft: '0.5rem' }}>
                      ({p.siswa?.email})
                    </span>
                  </div>
                  <span className="badge badge-neutral" style={{ fontSize: '0.75rem' }}>
                    Dikumpulkan: {p.dikumpulkan_at}
                  </span>
                </div>

                {p.catatan && (
                  <p style={{ fontSize: '0.825rem', color: 'var(--text-light)', marginBottom: '0.5rem' }}>
                    <em>"{p.catatan}"</em>
                  </p>
                )}

                <div style={{ display: 'flex', gap: '0.75rem', alignItems: 'center', marginTop: '0.75rem', flexWrap: 'wrap' }}>
                  {p.file_path && (
                    <a
                      href={`${STORAGE_URL}${p.file_path}`}
                      target="_blank"
                      rel="noreferrer"
                      className="btn btn-sm btn-outline"
                    >
                      📥 Unduh Berkas Siswa
                    </a>
                  )}

                  <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', marginLeft: 'auto' }}>
                    <input
                      type="number"
                      placeholder="Nilai (0-100)"
                      value={gradingScores[p.id] ?? ''}
                      onChange={(e) => setGradingScores({ ...gradingScores, [p.id]: e.target.value })}
                      style={{ width: '100px', padding: '0.35rem 0.5rem', borderRadius: '4px', border: '1px solid var(--border)' }}
                      min={0}
                      max={100}
                    />
                    <input
                      type="text"
                      placeholder="Catatan feedback..."
                      value={gradingFeedbacks[p.id] ?? ''}
                      onChange={(e) => setGradingFeedbacks({ ...gradingFeedbacks, [p.id]: e.target.value })}
                      style={{ width: '180px', padding: '0.35rem 0.5rem', borderRadius: '4px', border: '1px solid var(--border)' }}
                    />
                    <Button size="sm" variant="primary" onClick={() => handleSaveScore(p.id)}>
                      Simpan
                    </Button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </Modal>
    </div>
  )
}
