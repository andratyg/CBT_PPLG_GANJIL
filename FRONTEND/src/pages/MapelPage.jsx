import React, { useState, useEffect } from 'react'
import api from '../api'
import Card from '../components/Card'
import Table from '../components/Table'
import Button from '../components/Button'
import Modal from '../components/Modal'
import FormInput from '../components/FormInput'

export default function MapelPage({ user }) {
  const [mapelList, setMapelList] = useState([])
  const [loading, setLoading] = useState(true)
  const [search, setSearch] = useState('')
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [editingItem, setEditingItem] = useState(null)
  const [deleteConfirmItem, setDeleteConfirmItem] = useState(null)
  const [submitting, setSubmitting] = useState(false)
  const [errorMsg, setErrorMsg] = useState('')

  const [form, setForm] = useState({
    nama: '',
    deskripsi: '',
    guru_id: user?.id || ''
  })

  const isGuru = user?.role === 'guru'

  const loadMapel = () => {
    setLoading(true)
    api.get('/mapel')
      .then((res) => setMapelList(res.data || []))
      .catch((err) => console.error('Gagal memuat mapel:', err))
      .finally(() => setLoading(false))
  }

  useEffect(() => {
    loadMapel()
  }, [])

  const openAddModal = () => {
    setEditingItem(null)
    setForm({
      nama: '',
      deskripsi: '',
      guru_id: user?.id || ''
    })
    setErrorMsg('')
    setIsModalOpen(true)
  }

  const openEditModal = (item) => {
    setEditingItem(item)
    setForm({
      nama: item.nama,
      deskripsi: item.deskripsi || '',
      guru_id: item.guru_id || user?.id || ''
    })
    setErrorMsg('')
    setIsModalOpen(true)
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSubmitting(true)
    setErrorMsg('')

    const token = localStorage.getItem('token')
    if (!token) {
      setErrorMsg('Sesi login Guru belum aktif atau telah kedaluwarsa. Mengarahkan ke halaman login...')
      setSubmitting(false)
      setTimeout(() => { window.location.href = '/login' }, 1200)
      return
    }

    try {
      const payload = {
        nama: form.nama,
        deskripsi: form.deskripsi,
        guru_id: Number(form.guru_id) || user?.id || 1
      }

      if (editingItem) {
        await api.put(`/mapel/${editingItem.id}`, payload)
      } else {
        await api.post('/mapel', payload)
      }

      setIsModalOpen(false)
      loadMapel()
    } catch (err) {
      console.error(err)
      if (err.response?.status === 401) {
        setErrorMsg('Sesi login telah kedaluwarsa (401 Unauthorized). Mengarahkan ke login...')
        setTimeout(() => { window.location.href = '/login' }, 1500)
      } else {
        setErrorMsg(err.response?.data?.message || 'Gagal menyimpan data mata pelajaran.')
      }
    } finally {
      setSubmitting(false)
    }
  }

  const handleDelete = async () => {
    if (!deleteConfirmItem) return
    setSubmitting(true)
    try {
      await api.delete(`/mapel/${deleteConfirmItem.id}`)
      setDeleteConfirmItem(null)
      loadMapel()
    } catch (err) {
      console.error(err)
      alert('Gagal menghapus mata pelajaran.')
    } finally {
      setSubmitting(false)
    }
  }

  const filteredMapel = mapelList.filter((m) =>
    m.nama?.toLowerCase().includes(search.toLowerCase()) ||
    m.deskripsi?.toLowerCase().includes(search.toLowerCase()) ||
    m.guru?.name?.toLowerCase().includes(search.toLowerCase())
  )

  const columns = [
    {
      header: 'No',
      key: 'no',
      width: '60px',
      align: 'center',
      render: (_, idx) => <span className="text-muted">{idx + 1}</span>
    },
    {
      header: 'Nama Mata Pelajaran',
      key: 'nama',
      render: (row) => (
        <div>
          <strong style={{ fontSize: '0.925rem', color: 'var(--text)' }}>{row.nama}</strong>
          <div style={{ fontSize: '0.8rem', color: 'var(--muted)', marginTop: '0.15rem' }}>
            {row.deskripsi || 'Tidak ada deskripsi'}
          </div>
        </div>
      )
    },
    {
      header: 'Guru Pengampu',
      key: 'guru',
      width: '200px',
      render: (row) => (
        <span className="badge badge-accent">
          👨‍🏫 {row.guru?.name || `Guru ID #${row.guru_id}`}
        </span>
      )
    },
    ...(isGuru
      ? [
          {
            header: 'Aksi',
            key: 'aksi',
            width: '150px',
            align: 'right',
            render: (row) => (
              <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '0.4rem' }}>
                <Button
                  size="sm"
                  variant="outline"
                  onClick={() => openEditModal(row)}
                  title="Edit Mapel"
                >
                  ✏️ Edit
                </Button>
                <Button
                  size="sm"
                  variant="danger"
                  onClick={() => setDeleteConfirmItem(row)}
                  title="Hapus Mapel"
                >
                  🗑️
                </Button>
              </div>
            )
          }
        ]
      : [])
  ]

  return (
    <div>
      <div className="page-header">
        <div>
          <h1 className="page-title">
            <span>Mata Pelajaran PPLG</span>
            <span className="badge badge-neutral">{mapelList.length} Mapel</span>
          </h1>
          <p className="page-subtitle">Daftar kurikulum kejuruan Pengembangan Perangkat Lunak dan Gim</p>
        </div>
        {isGuru && (
          <Button variant="primary" icon="➕" onClick={openAddModal}>
            Tambah Mapel
          </Button>
        )}
      </div>

      <Card>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1.25rem', flexWrap: 'wrap', gap: '0.75rem' }}>
          <div className="search-input-wrap">
            <span className="search-icon">🔍</span>
            <input
              type="text"
              className="form-input"
              placeholder="Cari mata pelajaran atau guru..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
          </div>
          <div className="text-muted" style={{ fontSize: '0.825rem' }}>
            Menampilkan {filteredMapel.length} dari {mapelList.length} data
          </div>
        </div>

        <Table
          columns={columns}
          data={filteredMapel}
          loading={loading}
          emptyMessage="Tidak ada mata pelajaran yang cocok dengan pencarian"
        />
      </Card>

      {/* Modal Add / Edit */}
      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title={editingItem ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran Baru'}
        footer={
          <>
            <Button variant="secondary" onClick={() => setIsModalOpen(false)} disabled={submitting}>
              Batal
            </Button>
            <Button variant="primary" onClick={handleSubmit} loading={submitting}>
              {editingItem ? 'Perbarui Mapel' : 'Simpan Mapel'}
            </Button>
          </>
        }
      >
        <form onSubmit={handleSubmit}>
          {errorMsg && (
            <div className="badge badge-danger mb-4" style={{ width: '100%', padding: '0.6rem', borderRadius: 'var(--radius-sm)' }}>
              ⚠️ {errorMsg}
            </div>
          )}

          <FormInput
            label="Nama Mata Pelajaran"
            name="nama"
            value={form.nama}
            onChange={(e) => setForm({ ...form, nama: e.target.value })}
            placeholder="Contoh: Pemodelan Perangkat Lunak / Web Development"
            required
          />

          <FormInput
            type="textarea"
            label="Deskripsi / Capaian Pembelajaran"
            name="deskripsi"
            value={form.deskripsi}
            onChange={(e) => setForm({ ...form, deskripsi: e.target.value })}
            placeholder="Keterangan materi pokok yang diajarkan pada mapel ini..."
            rows={3}
          />

          <FormInput
            type="number"
            label="ID Guru Pengampu"
            name="guru_id"
            value={form.guru_id}
            onChange={(e) => setForm({ ...form, guru_id: e.target.value })}
            helperText="Default adalah ID akun Anda yang sedang aktif"
            required
          />
        </form>
      </Modal>

      {/* Modal Konfirmasi Hapus */}
      <Modal
        isOpen={Boolean(deleteConfirmItem)}
        onClose={() => setDeleteConfirmItem(null)}
        title="Konfirmasi Hapus Mata Pelajaran"
        size="sm"
        footer={
          <>
            <Button variant="secondary" onClick={() => setDeleteConfirmItem(null)} disabled={submitting}>
              Batal
            </Button>
            <Button variant="danger" onClick={handleDelete} loading={submitting}>
              Ya, Hapus
            </Button>
          </>
        }
      >
        <p style={{ color: 'var(--text-light)', fontSize: '0.9rem' }}>
          Apakah Anda yakin ingin menghapus mata pelajaran <strong>{deleteConfirmItem?.nama}</strong>?
          Tindakan ini tidak dapat dibatalkan.
        </p>
      </Modal>
    </div>
  )
}
