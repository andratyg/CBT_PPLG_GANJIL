import React, { useState, useEffect } from 'react'
import api from '../api'
import Card from '../components/Card'
import Button from '../components/Button'
import Modal from '../components/Modal'
import FormInput from '../components/FormInput'

const STORAGE_URL = import.meta.env.VITE_STORAGE_URL || 'http://localhost:8000/storage/'

export default function PertemuanMateriPage({ user }) {
  const [pertemuanList, setPertemuanList] = useState([])
  const [jadwalList, setJadwalList] = useState([])
  const [loading, setLoading] = useState(true)
  const [selectedPertemuan, setSelectedPertemuan] = useState(null)
  const [materiList, setMateriList] = useState([])
  const [loadingMateri, setLoadingMateri] = useState(false)

  // Modals
  const [isAddPertemuanOpen, setIsAddPertemuanOpen] = useState(false)
  const [isUploadMateriOpen, setIsUploadMateriOpen] = useState(false)
  const [submitting, setSubmitting] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

  const [pertemuanForm, setPertemuanForm] = useState({
    jadwal_id: '',
    tanggal: new Date().toISOString().split('T')[0],
    pertemuan_ke: 1,
    topik: '',
    catatan: ''
  })

  const [materiForm, setMateriForm] = useState({
    judul: '',
    konten: '',
    file: null
  })

  const isGuru = user?.role === 'guru'

  const loadData = () => {
    setLoading(true)
    Promise.allSettled([
      api.get('/pertemuan'),
      api.get('/jadwal')
    ])
      .then(([pRes, jRes]) => {
        if (pRes.status === 'fulfilled') {
          const list = pRes.value.data || []
          setPertemuanList(list)
          if (list.length > 0 && !selectedPertemuan) {
            viewPertemuan(list[0])
          }
        }
        if (jRes.status === 'fulfilled') {
          setJadwalList(jRes.value.data || [])
        }
      })
      .catch(console.error)
      .finally(() => setLoading(false))
  }

  const viewPertemuan = async (p) => {
    setSelectedPertemuan(p)
    setLoadingMateri(true)
    try {
      const res = await api.get(`/pertemuan/${p.id}/materi`)
      setMateriList(res.data || [])
    } catch (err) {
      console.error('Gagal mengambil materi:', err)
      setMateriList(p.materi || [])
    } finally {
      setLoadingMateri(false)
    }
  }

  useEffect(() => {
    loadData()
  }, [])

  const handleCreatePertemuan = async (e) => {
    e.preventDefault()
    setSubmitting(true)
    setErrorMsg('')
    try {
      const payload = {
        jadwal_id: Number(pertemuanForm.jadwal_id),
        tanggal: pertemuanForm.tanggal,
        pertemuan_ke: Number(pertemuanForm.pertemuan_ke),
        topik: pertemuanForm.topik,
        catatan: pertemuanForm.catatan
      }
      const res = await api.post('/pertemuan', payload)
      setIsAddPertemuanOpen(false)
      setPertemuanForm({
        jadwal_id: jadwalList[0]?.id || '',
        tanggal: new Date().toISOString().split('T')[0],
        pertemuan_ke: (pertemuanList.length || 0) + 1,
        topik: '',
        catatan: ''
      })
      loadData()
      if (res.data) {
        viewPertemuan(res.data)
      }
    } catch (err) {
      console.error(err)
      setErrorMsg(err.response?.data?.message || 'Gagal membuat sesi pertemuan.')
    } finally {
      setSubmitting(false)
    }
  }

  const handleUploadMateri = async (e) => {
    e.preventDefault()
    if (!selectedPertemuan) return
    setSubmitting(true)
    setErrorMsg('')
    try {
      const formData = new FormData()
      formData.append('judul', materiForm.judul)
      if (materiForm.konten) formData.append('konten', materiForm.konten)
      if (materiForm.file) formData.append('file', materiForm.file)

      await api.post(`/pertemuan/${selectedPertemuan.id}/materi`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })

      setIsUploadMateriOpen(false)
      setMateriForm({ judul: '', konten: '', file: null })
      viewPertemuan(selectedPertemuan)
    } catch (err) {
      console.error(err)
      setErrorMsg(err.response?.data?.message || 'Gagal mengunggah materi.')
    } finally {
      setSubmitting(false)
    }
  }

  const handleDeleteMateri = async (materiId) => {
    if (!confirm('Hapus materi ajar ini?')) return
    try {
      await api.delete(`/materi/${materiId}`)
      viewPertemuan(selectedPertemuan)
    } catch (err) {
      console.error(err)
      alert('Gagal menghapus materi.')
    }
  }

  return (
    <div>
      <div className="page-header">
        <div>
          <h1 className="page-title">
            <span>Pertemuan & Materi Ajar</span>
            <span className="badge badge-neutral">{pertemuanList.length} Sesi</span>
          </h1>
          <p className="page-subtitle">Modul ajar, slide presentasi, dokumen PDF, dan silabus tiap sesi</p>
        </div>
        {isGuru && (
          <Button
            variant="primary"
            icon="➕"
            onClick={() => {
              setPertemuanForm({
                jadwal_id: jadwalList[0]?.id || '',
                tanggal: new Date().toISOString().split('T')[0],
                pertemuan_ke: (pertemuanList.length || 0) + 1,
                topik: '',
                catatan: ''
              })
              setErrorMsg('')
              setIsAddPertemuanOpen(true)
            }}
          >
            Tambah Pertemuan
          </Button>
        )}
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: 'minmax(300px, 380px) 1fr', gap: '1.5rem', alignItems: 'flex-start' }}>
        {/* Left Column: List Pertemuan */}
        <Card title="Daftar Sesi Pertemuan" subtitle="Klik pertemuan untuk melihat materi">
          {loading ? (
            <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--muted)' }}>
              Memuat pertemuan...
            </div>
          ) : pertemuanList.length === 0 ? (
            <div className="empty-state" style={{ padding: '2rem' }}>
              <div className="empty-state-icon">📚</div>
              <div className="empty-state-title">Belum ada pertemuan</div>
              <p className="text-muted" style={{ fontSize: '0.825rem' }}>Klik tombol di atas untuk membuat pertemuan ke-1.</p>
            </div>
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
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '0.3rem' }}>
                      <span className="badge badge-accent" style={{ fontSize: '0.7rem' }}>
                        Pertemuan {p.pertemuan_ke}
                      </span>
                      <span style={{ fontSize: '0.75rem', color: 'var(--muted)', fontWeight: 500 }}>
                        📅 {p.tanggal}
                      </span>
                    </div>
                    <div style={{ fontWeight: 700, fontSize: '0.9rem', color: 'var(--text)' }}>
                      {p.topik}
                    </div>
                    <div style={{ fontSize: '0.775rem', color: 'var(--muted)', marginTop: '0.2rem' }}>
                      Mapel: {p.jadwal?.mapel?.nama || '–'} • Kelas: {p.jadwal?.kelas?.nama || '–'}
                    </div>
                  </div>
                )
              })}
            </div>
          )}
        </Card>

        {/* Right Column: Selected Pertemuan Details & Materi */}
        <div>
          {selectedPertemuan ? (
            <Card
              title={`Pertemuan Ke-${selectedPertemuan.pertemuan_ke}: ${selectedPertemuan.topik}`}
              subtitle={`Tanggal: ${selectedPertemuan.tanggal} • ${selectedPertemuan.jadwal?.mapel?.nama || 'Mapel PPLG'} (${selectedPertemuan.jadwal?.kelas?.nama || 'Kelas'})`}
              action={
                isGuru && (
                  <Button
                    size="sm"
                    variant="primary"
                    icon="📤"
                    onClick={() => {
                      setErrorMsg('')
                      setIsUploadMateriOpen(true)
                    }}
                  >
                    Upload Materi
                  </Button>
                )
              }
            >
              {selectedPertemuan.catatan && (
                <div
                  style={{
                    background: 'var(--card-subtle)',
                    padding: '0.85rem 1rem',
                    borderRadius: 'var(--radius-md)',
                    border: '1px solid var(--border)',
                    marginBottom: '1.25rem',
                    fontSize: '0.85rem',
                    color: 'var(--text-light)'
                  }}
                >
                  <strong>Catatan Guru:</strong> {selectedPertemuan.catatan}
                </div>
              )}

              <h4 style={{ fontSize: '0.95rem', fontWeight: 700, marginBottom: '0.75rem', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                <span>Berkas Materi Pembelajaran</span>
                <span className="badge badge-info">{materiList.length} Berkas</span>
              </h4>

              {loadingMateri ? (
                <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--muted)' }}>
                  Memuat berkas materi...
                </div>
              ) : materiList.length === 0 ? (
                <div className="empty-state" style={{ padding: '2rem', background: 'var(--card-subtle)', borderRadius: 'var(--radius-md)' }}>
                  <div className="empty-state-icon">📄</div>
                  <div className="empty-state-title">Belum ada materi diunggah</div>
                  <p className="text-muted" style={{ fontSize: '0.825rem' }}>
                    Unggah bahan ajar berupa PDF, slide, dokumen atau ringkasan teori.
                  </p>
                </div>
              ) : (
                <div style={{ display: 'flex', flexDirection: 'column', gap: '0.75rem' }}>
                  {materiList.map((m) => {
                    const ext = m.tipe_file?.toLowerCase() || 'file'
                    const fileUrl = m.file_path ? `${STORAGE_URL}${m.file_path}` : null
                    return (
                      <div
                        key={m.id}
                        style={{
                          display: 'flex',
                          alignItems: 'center',
                          justifyContent: 'space-between',
                          padding: '0.85rem 1rem',
                          background: 'var(--card)',
                          border: '1px solid var(--border)',
                          borderRadius: 'var(--radius-md)',
                          boxShadow: 'var(--shadow-sm)'
                        }}
                      >
                        <div style={{ display: 'flex', alignItems: 'center', gap: '0.85rem' }}>
                          <div
                            style={{
                              width: '40px',
                              height: '40px',
                              borderRadius: 'var(--radius-sm)',
                              background: 'var(--accent-light)',
                              color: 'var(--accent-hover)',
                              display: 'flex',
                              alignItems: 'center',
                              justifyContent: 'center',
                              fontWeight: 800,
                              fontSize: '0.75rem',
                              textTransform: 'uppercase'
                            }}
                          >
                            {ext}
                          </div>
                          <div>
                            <div style={{ fontWeight: 600, fontSize: '0.9rem', color: 'var(--text)' }}>
                              {m.judul}
                            </div>
                            {m.konten && (
                              <div style={{ fontSize: '0.8rem', color: 'var(--muted)', marginTop: '0.1rem' }}>
                                {m.konten}
                              </div>
                            )}
                          </div>
                        </div>

                        <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                          {fileUrl && (
                            <a
                              href={fileUrl}
                              target="_blank"
                              rel="noreferrer"
                              className="btn btn-sm btn-outline"
                              style={{ textDecoration: 'none' }}
                            >
                              ⬇️ Unduh File
                            </a>
                          )}
                          {isGuru && (
                            <Button
                              size="sm"
                              variant="ghost"
                              onClick={() => handleDeleteMateri(m.id)}
                              title="Hapus materi"
                            >
                              🗑️
                            </Button>
                          )}
                        </div>
                      </div>
                    )
                  })}
                </div>
              )}
            </Card>
          ) : (
            <Card>
              <div className="empty-state" style={{ padding: '3rem' }}>
                <div className="empty-state-icon">👈</div>
                <div className="empty-state-title">Pilih salah satu sesi pertemuan</div>
                <p className="text-muted" style={{ fontSize: '0.85rem' }}>
                  Pilih dari panel sebelah kiri untuk melihat materi dan modul pembelajaran.
                </p>
              </div>
            </Card>
          )}
        </div>
      </div>

      {/* Modal Buat Pertemuan */}
      <Modal
        isOpen={isAddPertemuanOpen}
        onClose={() => setIsAddPertemuanOpen(false)}
        title="Buat Sesi Pertemuan Baru"
        footer={
          <>
            <Button variant="secondary" onClick={() => setIsAddPertemuanOpen(false)} disabled={submitting}>
              Batal
            </Button>
            <Button variant="primary" onClick={handleCreatePertemuan} loading={submitting}>
              Simpan Pertemuan
            </Button>
          </>
        }
      >
        <form onSubmit={handleCreatePertemuan}>
          {errorMsg && (
            <div className="badge badge-danger mb-4" style={{ width: '100%', padding: '0.6rem' }}>
              ⚠️ {errorMsg}
            </div>
          )}

          <FormInput
            type="select"
            label="Jadwal Mata Pelajaran & Kelas"
            name="jadwal_id"
            value={pertemuanForm.jadwal_id}
            onChange={(e) => setPertemuanForm({ ...pertemuanForm, jadwal_id: e.target.value })}
            options={jadwalList.map((j) => ({
              value: j.id,
              label: `${j.mapel?.nama} — Kelas ${j.kelas?.nama} (${j.hari.toUpperCase()})`
            }))}
            placeholder="Pilih jadwal mengajar..."
            required
          />

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
            <FormInput
              type="number"
              label="Pertemuan Ke"
              name="pertemuan_ke"
              min={1}
              value={pertemuanForm.pertemuan_ke}
              onChange={(e) => setPertemuanForm({ ...pertemuanForm, pertemuan_ke: e.target.value })}
              required
            />
            <FormInput
              type="date"
              label="Tanggal"
              name="tanggal"
              value={pertemuanForm.tanggal}
              onChange={(e) => setPertemuanForm({ ...pertemuanForm, tanggal: e.target.value })}
              required
            />
          </div>

          <FormInput
            label="Topik / Pokok Bahasan"
            name="topik"
            value={pertemuanForm.topik}
            onChange={(e) => setPertemuanForm({ ...pertemuanForm, topik: e.target.value })}
            placeholder="Contoh: Pengenalan Clean Architecture & React Hooks"
            required
          />

          <FormInput
            type="textarea"
            label="Catatan / Instruksi Pengantar"
            name="catatan"
            value={pertemuanForm.catatan}
            onChange={(e) => setPertemuanForm({ ...pertemuanForm, catatan: e.target.value })}
            placeholder="Instruksi awal untuk siswa sebelum memulai sesi..."
            rows={3}
          />
        </form>
      </Modal>

      {/* Modal Upload Materi */}
      <Modal
        isOpen={isUploadMateriOpen}
        onClose={() => setIsUploadMateriOpen(false)}
        title={`Upload Materi Ajar — Pertemuan ${selectedPertemuan?.pertemuan_ke}`}
        footer={
          <>
            <Button variant="secondary" onClick={() => setIsUploadMateriOpen(false)} disabled={submitting}>
              Batal
            </Button>
            <Button variant="primary" onClick={handleUploadMateri} loading={submitting}>
              Unggah Materi
            </Button>
          </>
        }
      >
        <form onSubmit={handleUploadMateri}>
          {errorMsg && (
            <div className="badge badge-danger mb-4" style={{ width: '100%', padding: '0.6rem' }}>
              ⚠️ {errorMsg}
            </div>
          )}

          <FormInput
            label="Judul Materi / Modul"
            name="judul"
            value={materiForm.judul}
            onChange={(e) => setMateriForm({ ...materiForm, judul: e.target.value })}
            placeholder="Contoh: Modul 01 - Dasar Komponen React.pdf"
            required
          />

          <FormInput
            type="textarea"
            label="Keterangan Ringkas (Opsional)"
            name="konten"
            value={materiForm.konten}
            onChange={(e) => setMateriForm({ ...materiForm, konten: e.target.value })}
            placeholder="Ringkasan poin materi atau link referensi tambahan..."
            rows={2}
          />

          <FormInput
            type="file"
            label="Lampirkan Berkas (PDF, DOCX, PPT, ZIP)"
            name="file"
            onChange={(e) => setMateriForm({ ...materiForm, file: e.target.files[0] })}
            helperText="Ukuran maksimal berkas 50 MB"
          />
        </form>
      </Modal>
    </div>
  )
}
