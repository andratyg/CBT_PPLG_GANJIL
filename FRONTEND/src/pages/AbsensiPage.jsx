import React, { useState, useEffect, useMemo } from 'react'
import api from '../api'
import Card from '../components/Card'
import Button from '../components/Button'
import { exportToExcel, exportToCSV, printAttendanceReport, exportMatrixToExcel } from '../utils/exportAttendance'

export default function AbsensiPage({ user }) {
  // Mode: 'tunggal' (per pertemuan) | 'matriks' (multi-pertemuan sekaligus)
  const [viewMode, setViewMode] = useState('tunggal')

  // Filter Kelas & Jadwal / Jam Pelajaran (Bu Yayu)
  const [kelasList, setKelasList] = useState([])
  const [jadwalList, setJadwalList] = useState([])
  const [selectedKelasId, setSelectedKelasId] = useState(() => {
    const p = new URLSearchParams(window.location.search)
    return p.get('kelas_id') || '1'
  })
  const [selectedJadwalId, setSelectedJadwalId] = useState('semua')
  const [activeJurusanFilter, setActiveJurusanFilter] = useState('semua')

  // Data Pertemuan Tunggal
  const [pertemuanList, setPertemuanList] = useState([])
  const [selectedPertemuanId, setSelectedPertemuanId] = useState('')
  const [siswaList, setSiswaList] = useState([])
  const [absensiMap, setAbsensiMap] = useState({}) // { [siswa_id]: { status: 'hadir', keterangan: '' } }
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [successMsg, setSuccessMsg] = useState('')

  // Modal Buka Pertemuan Baru
  const [isNewMeetingModalOpen, setIsNewMeetingModalOpen] = useState(false)
  const [creatingMeeting, setCreatingMeeting] = useState(false)
  const [newMeetingForm, setNewMeetingForm] = useState({
    jadwal_id: '',
    tanggal: '2026-09-08',
    pertemuan_ke: 1,
    topik: '',
    catatan: ''
  })

  // Data Matriks Multi-Pertemuan
  const [matrixData, setMatrixData] = useState(null)
  const [matrixDraft, setMatrixDraft] = useState({}) // { [siswa_id]: { [pertemuan_id]: { status, keterangan } } }
  const [loadingMatrix, setLoadingMatrix] = useState(false)
  const [savingMatrix, setSavingMatrix] = useState(false)
  const [targetBatchPertemuanId, setTargetBatchPertemuanId] = useState('')

  // Pencarian & Filter
  const [searchQuery, setSearchQuery] = useState('')
  const [statusFilter, setStatusFilter] = useState('semua')

  // Helper konversi jam mulai-selesai ke JP (Jam Pelajaran) sekolah
  const formatJamPelajaran = (jamMulai, jamSelesai) => {
    if (!jamMulai) return ''
    const start = jamMulai.substring(0, 5)
    const end = jamSelesai ? jamSelesai.substring(0, 5) : ''
    
    let jp = ''
    if (start === '07:30' && (end === '09:00' || end === '09:30')) {
      jp = end === '09:00' ? 'Jam Ke-1 s.d 2' : 'Jam Ke-1 s.d 3'
    } else if (start === '08:00' && end === '09:30') {
      jp = 'Jam Ke-2 s.d 3'
    } else if (start === '09:45' && end === '11:45') {
      jp = 'Jam Ke-4 s.d 6'
    } else if (start === '10:00') {
      jp = 'Jam Ke-5 s.d 6'
    } else {
      jp = 'Sesi Jam'
    }
    return `${jp} (${start} – ${end} WIB)`
  }

  // Load data pertemuan tunggal
  const loadSiswaAndAbsensi = async (pertemuanId) => {
    if (!pertemuanId) return
    setLoading(true)
    setSuccessMsg('')
    try {
      const [siswaRes, absensiRes] = await Promise.all([
        api.get(`/pertemuan/${pertemuanId}/siswa`),
        api.get(`/pertemuan/${pertemuanId}/absensi`)
      ])

      const siswas = siswaRes.data || []
      setSiswaList(siswas)

      const map = {}
      siswas.forEach((s) => {
        map[s.id] = { status: 'hadir', keterangan: '' }
      })

      if (absensiRes.data && absensiRes.data.length > 0) {
        absensiRes.data.forEach((row) => {
          map[row.siswa_id] = {
            status: row.status,
            keterangan: row.keterangan || ''
          }
        })
      }

      setAbsensiMap(map)
    } catch (err) {
      console.error(err)
    } finally {
      setLoading(false)
    }
  }

  // Load data matriks seluruh pertemuan (bisa difilter kelas & jadwal)
  const loadMatrixData = async (kelasId = selectedKelasId, jadwalId = selectedJadwalId) => {
    setLoadingMatrix(true)
    try {
      const params = {}
      if (kelasId && kelasId !== 'semua') params.kelas_id = kelasId
      if (jadwalId && jadwalId !== 'semua') params.jadwal_id = jadwalId

      const res = await api.get('/absensi/matrix', { params })
      const data = res.data
      setMatrixData(data)

      // Inisialisasi draft matriks
      const draft = {}
      const siswas = data.siswa || []
      const pertemuans = data.pertemuan || []

      siswas.forEach((s) => {
        draft[s.id] = {}
        pertemuans.forEach((p) => {
          const existing = data.matrix?.[s.id]?.[p.id]
          draft[s.id][p.id] = {
            status: existing?.status || 'hadir',
            keterangan: existing?.keterangan || ''
          }
        })
      })

      setMatrixDraft(draft)
      if (pertemuans.length > 0) {
        setTargetBatchPertemuanId(String(pertemuans[0].id))
      }
    } catch (err) {
      console.error('Gagal memuat matriks absensi:', err)
    } finally {
      setLoadingMatrix(false)
    }
  }

  // Inisialisasi data dari backend
  useEffect(() => {
    Promise.all([
      api.get('/kelas'),
      api.get('/jadwal'),
      api.get('/pertemuan')
    ])
      .then(([kRes, jRes, pRes]) => {
        const kList = kRes.data || []
        const jList = jRes.data || []
        const pList = pRes.data || []

        setKelasList(kList)
        setJadwalList(jList)
        setPertemuanList(pList)

        // Default ke kelas dari query param atau kelas pertama
        const urlParams = new URLSearchParams(window.location.search)
        const paramKId = urlParams.get('kelas_id')
        const defKelasId = paramKId && kList.some((k) => String(k.id) === String(paramKId))
          ? String(paramKId)
          : (kList.length > 0 ? String(kList[0].id) : '1')
        setSelectedKelasId(defKelasId)

        // Saring pertemuan kelas pertama
        const matched = pList.filter((p) => String(p.jadwal?.kelas_id) === defKelasId)
        if (matched.length > 0) {
          setSelectedPertemuanId(matched[0].id)
          setTargetBatchPertemuanId(String(matched[0].id))
          loadSiswaAndAbsensi(matched[0].id)
        } else if (pList.length > 0) {
          setSelectedPertemuanId(pList[0].id)
          setTargetBatchPertemuanId(String(pList[0].id))
          loadSiswaAndAbsensi(pList[0].id)
        } else {
          setLoading(false)
        }

        loadMatrixData(defKelasId, 'semua')
      })
      .catch((err) => {
        console.error(err)
        setLoading(false)
      })
  }, [])

  // Jadwal & Pertemuan tersaring berdasarkan kelas & jadwal yang dipilih
  const filteredJadwalList = useMemo(() => {
    if (selectedKelasId === 'semua') return jadwalList
    return jadwalList.filter((j) => String(j.kelas_id) === String(selectedKelasId))
  }, [jadwalList, selectedKelasId])

  const filteredPertemuanList = useMemo(() => {
    return pertemuanList.filter((p) => {
      const matchKelas = selectedKelasId === 'semua' || String(p.jadwal?.kelas_id) === String(selectedKelasId)
      const matchJadwal = selectedJadwalId === 'semua' || String(p.jadwal_id) === String(selectedJadwalId)
      return matchKelas && matchJadwal
    })
  }, [pertemuanList, selectedKelasId, selectedJadwalId])

  // Handler Ganti Kelas
  const handleSelectKelas = (kId) => {
    setSelectedKelasId(kId)
    setSelectedJadwalId('semua')

    const matched = pertemuanList.filter((p) => kId === 'semua' || String(p.jadwal?.kelas_id) === String(kId))
    if (matched.length > 0) {
      setSelectedPertemuanId(matched[0].id)
      setTargetBatchPertemuanId(String(matched[0].id))
      loadSiswaAndAbsensi(matched[0].id)
    } else {
      setSelectedPertemuanId('')
      setSiswaList([])
      setAbsensiMap({})
    }

    loadMatrixData(kId, 'semua')
  }

  // Handler Ganti Jadwal / Jam Pelajaran
  const handleSelectJadwal = (jId) => {
    setSelectedJadwalId(jId)

    const matched = pertemuanList.filter((p) => {
      const matchKelas = selectedKelasId === 'semua' || String(p.jadwal?.kelas_id) === String(selectedKelasId)
      const matchJadwal = jId === 'semua' || String(p.jadwal_id) === String(jId)
      return matchKelas && matchJadwal
    })

    if (matched.length > 0) {
      setSelectedPertemuanId(matched[0].id)
      setTargetBatchPertemuanId(String(matched[0].id))
      loadSiswaAndAbsensi(matched[0].id)
    } else {
      setSelectedPertemuanId('')
      setSiswaList([])
      setAbsensiMap({})
    }

    loadMatrixData(selectedKelasId, jId)
  }

  const handleSelectPertemuan = (id) => {
    setSelectedPertemuanId(id)
    loadSiswaAndAbsensi(id)
  }

  // Buka Modal Tambah Pertemuan Baru
  const openNewMeetingModal = () => {
    const applicableJadwals = filteredJadwalList.length > 0 ? filteredJadwalList : jadwalList
    const targetJadwal = selectedJadwalId !== 'semua'
      ? jadwalList.find((j) => String(j.id) === String(selectedJadwalId))
      : applicableJadwals[0]

    const nextKe = filteredPertemuanList.length + 1
    setNewMeetingForm({
      jadwal_id: targetJadwal?.id ? String(targetJadwal.id) : '',
      tanggal: '2026-09-08',
      pertemuan_ke: nextKe,
      topik: '',
      catatan: ''
    })
    setIsNewMeetingModalOpen(true)
  }

  const handleCreateNewMeeting = async (e) => {
    e.preventDefault()
    if (!newMeetingForm.jadwal_id || !newMeetingForm.topik) {
      alert('Silakan pilih jadwal/jam dan tulis topik pertemuan.')
      return
    }

    setCreatingMeeting(true)
    try {
      const res = await api.post('/pertemuan', newMeetingForm)
      const created = res.data
      setIsNewMeetingModalOpen(false)

      // Refresh list pertemuan
      const pRes = await api.get('/pertemuan')
      const updatedList = pRes.data || []
      setPertemuanList(updatedList)

      // Langsung arahkan ke pertemuan baru
      setSelectedPertemuanId(created.id)
      setTargetBatchPertemuanId(String(created.id))
      loadSiswaAndAbsensi(created.id)
      loadMatrixData(selectedKelasId, selectedJadwalId)

      setSuccessMsg(`Pertemuan Ke-${created.pertemuan_ke} berhasil dibuat dan siap diabsen!`)
      setTimeout(() => setSuccessMsg(''), 4000)
    } catch (err) {
      console.error(err)
      alert(err.response?.data?.message || 'Gagal membuat pertemuan baru.')
    } finally {
      setCreatingMeeting(false)
    }
  }

  // Single mode handlers
  const handleSetStatus = (siswaId, status) => {
    setAbsensiMap((prev) => ({
      ...prev,
      [siswaId]: { ...prev[siswaId], status }
    }))
  }

  const handleSetKeterangan = (siswaId, keterangan) => {
    setAbsensiMap((prev) => ({
      ...prev,
      [siswaId]: { ...prev[siswaId], keterangan }
    }))
  }

  const handleMarkAllHadir = () => {
    setAbsensiMap((prev) => {
      const next = { ...prev }
      Object.keys(next).forEach((k) => {
        next[k] = { ...next[k], status: 'hadir' }
      })
      return next
    })
  }

  const handleMarkUnmarkedAlpa = () => {
    setAbsensiMap((prev) => {
      const next = { ...prev }
      Object.keys(next).forEach((k) => {
        if (!next[k]?.status || next[k]?.status === 'alpa') {
          next[k] = { ...next[k], status: 'alpa', keterangan: 'Tidak hadir tanpa keterangan' }
        }
      })
      return next
    })
  }

  const handleSaveAbsensi = async () => {
    if (!selectedPertemuanId) return
    setSaving(true)
    setSuccessMsg('')
    try {
      const payload = {
        absensi: siswaList.map((s) => ({
          siswa_id: s.id,
          status: absensiMap[s.id]?.status || 'hadir',
          keterangan: absensiMap[s.id]?.keterangan || null
        }))
      }

      await api.post(`/pertemuan/${selectedPertemuanId}/absensi`, payload)
      setSuccessMsg('Presensi kehadiran sesi pertemuan ini berhasil disimpan!')
      loadMatrixData()
      setTimeout(() => setSuccessMsg(''), 4000)
    } catch (err) {
      console.error(err)
      alert('Gagal menyimpan presensi kehadiran.')
    } finally {
      setSaving(false)
    }
  }

  // Multi-Meeting Matrix handlers
  const cycleStatus = (current) => {
    const cycle = {
      hadir: 'izin',
      izin: 'sakit',
      sakit: 'dispen',
      dispen: 'alpa',
      alpa: 'hadir'
    }
    return cycle[current] || 'hadir'
  }

  const handleCellClick = (siswaId, pertemuanId) => {
    setMatrixDraft((prev) => {
      const current = prev[siswaId]?.[pertemuanId]?.status || 'hadir'
      const nextStatus = cycleStatus(current)
      return {
        ...prev,
        [siswaId]: {
          ...prev[siswaId],
          [pertemuanId]: {
            ...prev[siswaId]?.[pertemuanId],
            status: nextStatus
          }
        }
      }
    })
  }

  const handleBatchMarkMeetingHadir = () => {
    if (!targetBatchPertemuanId) return
    setMatrixDraft((prev) => {
      const next = { ...prev }
      Object.keys(next).forEach((sId) => {
        if (!next[sId]) next[sId] = {}
        next[sId][targetBatchPertemuanId] = {
          ...next[sId][targetBatchPertemuanId],
          status: 'hadir'
        }
      })
      return next
    })
    setSuccessMsg(`Semua siswa berhasil ditandai HADIR pada pertemuan yang dipilih!`)
    setTimeout(() => setSuccessMsg(''), 3000)
  }

  const handleMarkAllHadirForMeeting = (pertemuanId) => {
    setMatrixDraft((prev) => {
      const next = { ...prev }
      Object.keys(next).forEach((sId) => {
        if (!next[sId]) next[sId] = {}
        next[sId][pertemuanId] = {
          ...next[sId][pertemuanId],
          status: 'hadir'
        }
      })
      return next
    })
    setSuccessMsg(`Seluruh siswa di Pertemuan ini ditandai Hadir! Klik Simpan untuk memperbarui database.`)
    setTimeout(() => setSuccessMsg(''), 3000)
  }

  const unsavedMatrixCount = useMemo(() => {
    if (!matrixData || !matrixDraft) return 0
    let count = 0
    const pertemuans = matrixData.pertemuan || []
    const siswas = matrixData.siswa || []
    siswas.forEach((s) => {
      pertemuans.forEach((p) => {
        const originalStatus = matrixData.matrix?.[s.id]?.[p.id]?.status || 'hadir'
        const currentStatus = matrixDraft[s.id]?.[p.id]?.status || 'hadir'
        if (originalStatus !== currentStatus) {
          count++
        }
      })
    })
    return count
  }, [matrixData, matrixDraft])

  const handleSaveBatchMatrix = async () => {
    setSavingMatrix(true)
    setSuccessMsg('')
    try {
      const items = []
      Object.entries(matrixDraft).forEach(([sId, meetings]) => {
        Object.entries(meetings).forEach(([pId, data]) => {
          items.push({
            pertemuan_id: Number(pId),
            siswa_id: Number(sId),
            status: data.status || 'hadir',
            keterangan: data.keterangan || null
          })
        })
      })

      await api.post('/absensi/batch', { items })
      setSuccessMsg('Seluruh presensi multi-pertemuan berhasil disimpan serentak ke database!')
      loadMatrixData()
      if (selectedPertemuanId) loadSiswaAndAbsensi(selectedPertemuanId)
      setTimeout(() => setSuccessMsg(''), 4000)
    } catch (err) {
      console.error(err)
      alert('Gagal menyimpan data matriks absensi.')
    } finally {
      setSavingMatrix(false)
    }
  }

  const selectedPertemuan = pertemuanList.find((p) => String(p.id) === String(selectedPertemuanId))

  // Filtered Siswa untuk Single Mode
  const filteredSiswaList = siswaList.filter((s) => {
    const matchName = s.name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
                      s.email?.toLowerCase().includes(searchQuery.toLowerCase())
    const status = absensiMap[s.id]?.status || 'hadir'
    const matchStatus = statusFilter === 'semua' || status === statusFilter
    return matchName && matchStatus
  })

  // Filtered Rekap Siswa untuk Matrix Mode
  const filteredRekapSiswa = (matrixData?.rekap_siswa || []).filter((r) => {
    const matchName = r.siswa?.name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
                      r.siswa?.email?.toLowerCase().includes(searchQuery.toLowerCase())
    return matchName
  })

  // Summary counts for Single Mode
  const totalSiswa = siswaList.length
  const hadirCount = Object.values(absensiMap).filter((a) => a.status === 'hadir').length
  const izinCount = Object.values(absensiMap).filter((a) => a.status === 'izin').length
  const sakitCount = Object.values(absensiMap).filter((a) => a.status === 'sakit').length
  const dispenCount = Object.values(absensiMap).filter((a) => a.status === 'dispen').length
  const alpaCount = Object.values(absensiMap).filter((a) => a.status === 'alpa').length
  const persentaseHadir = totalSiswa > 0 ? Math.round(((hadirCount + dispenCount) / totalSiswa) * 100) : 0

  const getExportData = () => {
    return siswaList.map((s, idx) => ({
      no: idx + 1,
      name: s.name,
      email: s.email,
      status: absensiMap[s.id]?.status || 'hadir',
      keterangan: absensiMap[s.id]?.keterangan || ''
    }))
  }

  const handleExportExcel = () => {
    if (!selectedPertemuan) return
    exportToExcel({
      pertemuan: selectedPertemuan,
      data: getExportData(),
      summary: {
        total: totalSiswa,
        hadir: hadirCount,
        izin: izinCount,
        sakit: sakitCount,
        dispen: dispenCount,
        alpa: alpaCount
      }
    })
  }

  const handleExportCSV = () => {
    if (!selectedPertemuan) return
    exportToCSV({
      pertemuan: selectedPertemuan,
      data: getExportData()
    })
  }

  const handleExportMatrixExcel = () => {
    if (!matrixData) return
    const currentKelas = kelasList.find((k) => String(k.id) === String(selectedKelasId))
    exportMatrixToExcel({
      pertemuanList: matrixData.pertemuan || [],
      rekapSiswa: matrixData.rekap_siswa || [],
      kelasNama: currentKelas ? `Kelas ${currentKelas.nama}` : (matrixData.pertemuan?.[0]?.jadwal?.kelas?.nama || 'Kelas XII PPLG 1'),
      mapelNama: matrixData.pertemuan?.[0]?.jadwal?.mapel?.nama || 'Bahasa Indonesia',
      guruNama: user?.name || 'Bu Yayu'
    })
  }

  const statusBadges = {
    hadir: { label: 'Hadir', bg: '#ecfdf5', color: '#047857', border: '#a7f3d0' },
    izin: { label: 'Izin', bg: '#eff6ff', color: '#1d4ed8', border: '#bfdbfe' },
    sakit: { label: 'Sakit', bg: '#fffbeb', color: '#b45309', border: '#fde68a' },
    dispen: { label: 'Dispen', bg: '#f5f3ff', color: '#6d28d9', border: '#ddd6fe' },
    alpa: { label: 'Alpa', bg: '#fef2f2', color: '#b91c1c', border: '#fecaca' }
  }

  return (
    <div>
      {/* Header Halaman */}
      <div className="page-header" style={{ marginBottom: '1.25rem' }}>
        <div>
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.65rem', marginBottom: '0.25rem' }}>
            <h1 className="page-title">Presensi & Rekap Kehadiran Siswa</h1>
            <span className="badge badge-accent">Guru Pengampu</span>
          </div>
          <p className="page-subtitle">
            Kelola presensi per pertemuan atau input matriks multi-pertemuan serentak.
          </p>
        </div>

        {/* View Mode Toggle & Actions */}
        <div style={{ display: 'flex', alignItems: 'center', gap: '0.65rem', flexWrap: 'wrap' }}>
          {/* Mode Switcher */}
          <div style={{ display: 'inline-flex', background: 'var(--card-subtle)', padding: '3px', borderRadius: 'var(--radius-full)', border: '1px solid var(--border)' }}>
            <button
              type="button"
              className={`btn btn-sm ${viewMode === 'tunggal' ? 'btn-primary' : 'btn-ghost'}`}
              onClick={() => setViewMode('tunggal')}
              style={{ borderRadius: 'var(--radius-full)', padding: '0.35rem 0.85rem' }}
            >
              📋 Per Pertemuan
            </button>
            <button
              type="button"
              className={`btn btn-sm ${viewMode === 'matriks' ? 'btn-primary' : 'btn-ghost'}`}
              onClick={() => {
                setViewMode('matriks')
                loadMatrixData()
              }}
              style={{ borderRadius: 'var(--radius-full)', padding: '0.35rem 0.85rem' }}
            >
              📊 Matriks Multi-Pertemuan
            </button>
          </div>

          {/* Export Buttons */}
          {viewMode === 'tunggal' ? (
            <>
              <Button variant="outline" size="sm" icon="📊" onClick={handleExportExcel} title="Ekspor Pertemuan Ini ke Excel">
                Excel (.xls)
              </Button>
              <Button variant="outline" size="sm" icon="📄" onClick={handleExportCSV} title="Ekspor ke CSV">
                CSV
              </Button>
              <Button
                variant="outline"
                size="sm"
                icon="🖨️"
                onClick={() => {
                  if (!selectedPertemuan) return
                  printAttendanceReport({
                    pertemuan: selectedPertemuan,
                    data: getExportData(),
                    summary: {
                      total: totalSiswa,
                      hadir: hadirCount,
                      izin: izinCount,
                      sakit: sakitCount,
                      dispen: dispenCount,
                      alpa: alpaCount
                    }
                  })
                }}
              >
                Cetak / PDF
              </Button>
            </>
          ) : (
            <Button variant="primary" size="sm" icon="📊" onClick={handleExportMatrixExcel} title="Unduh Matriks Seluruh Pertemuan ke Excel">
              Ekspor Rekap Matriks Excel
            </Button>
          )}
        </div>
      </div>

      {/* Success Notification Banner */}
      {successMsg && (
        <div style={{ background: '#ecfdf5', border: '1px solid #a7f3d0', color: '#047857', padding: '0.85rem 1.25rem', borderRadius: 'var(--radius-md)', marginBottom: '1.25rem', fontWeight: 600, display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
          <span>✅</span>
          <span>{successMsg}</span>
        </div>
      )}

      {/* =========================================================================
          HERO CONTROL HUB: KELAS, JP & SESI PERTEMUAN (BU YAYU)
          ========================================================================= */}
      <div className="attendance-hero-card">
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '1.25rem', flexWrap: 'wrap', gap: '0.75rem', borderBottom: '1px solid var(--border)', paddingBottom: '0.85rem' }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.65rem' }}>
            <span style={{ fontSize: '1.2rem' }}>🎯</span>
            <div>
              <div style={{ fontSize: '0.725rem', fontWeight: 800, textTransform: 'uppercase', letterSpacing: '0.08em', color: 'var(--accent)' }}>
                Control Studio Presensi KBM
              </div>
              <div style={{ fontSize: '1.05rem', fontWeight: 800, color: 'var(--text)' }}>
                Pusat Kendali Kehadiran Guru • Bu Yayu
              </div>
            </div>
          </div>
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', fontSize: '0.8rem', color: 'var(--muted)' }}>
            <span className="badge badge-accent">Semester Ganjil 2026/2027</span>
            <span>•</span>
            <span>Selasa, 8 Sep 2026</span>
          </div>
        </div>

        <div className="control-hub-grid">
          {/* Hub 1: Pilih Kelas */}
          <div className="hub-section">
            <div className="hub-label">
              <span>🏫 1. Kelas Binaan</span>
              <span className="badge badge-neutral" style={{ fontSize: '0.65rem' }}>{kelasList.length} Kelas</span>
            </div>

            {/* Department Quick Switch Filter */}
            <div style={{ display: 'flex', gap: '0.3rem', marginBottom: '0.5rem', flexWrap: 'wrap' }}>
              {['semua', 'PPLG', 'TKJ', 'DKV', 'MPLB'].map((code) => {
                const isCodeActive = activeJurusanFilter === code
                return (
                  <button
                    key={code}
                    type="button"
                    onClick={() => setActiveJurusanFilter(code)}
                    style={{
                      padding: '0.15rem 0.5rem',
                      borderRadius: 'var(--radius-full)',
                      fontSize: '0.675rem',
                      fontWeight: 700,
                      cursor: 'pointer',
                      border: isCodeActive ? '1px solid var(--accent)' : '1px solid var(--border)',
                      background: isCodeActive ? 'var(--accent)' : 'transparent',
                      color: isCodeActive ? '#ffffff' : 'var(--muted)',
                      transition: 'all 0.15s ease'
                    }}
                  >
                    {code === 'semua' ? 'Semua' : code}
                  </button>
                )
              })}
            </div>

            <div className="class-pill-grid">
              {kelasList
                .filter((k) => {
                  if (activeJurusanFilter === 'semua') return true
                  return k.nama.toUpperCase().includes(activeJurusanFilter)
                })
                .map((k) => {
                  const isSelected = String(selectedKelasId) === String(k.id)
                  const upper = k.nama.toUpperCase()
                  const jurCode = upper.includes('PPLG') ? 'PPLG' : (upper.includes('TKJ') ? 'TKJ' : (upper.includes('DKV') ? 'DKV' : (upper.includes('MPLB') ? 'MPLB' : '')))
                  return (
                    <button
                      key={k.id}
                      type="button"
                      className={`class-pill-btn ${isSelected ? 'active' : ''}`}
                      onClick={() => handleSelectKelas(String(k.id))}
                      style={{
                        flexDirection: 'column',
                        alignItems: 'flex-start',
                        gap: '0.2rem',
                        padding: '0.45rem 0.6rem'
                      }}
                    >
                      <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', width: '100%', gap: '0.25rem' }}>
                        <span style={{ fontWeight: 800, fontSize: '0.8rem' }}>{k.nama}</span>
                        {jurCode && (
                          <span className={`badge badge-${jurCode.toLowerCase()}`} style={{ fontSize: '0.6rem', padding: '0.05rem 0.3rem' }}>
                            {jurCode}
                          </span>
                        )}
                      </div>
                      <span className="class-pill-count" style={{ fontSize: '0.65rem' }}>
                        {k.siswa_count || k.siswa?.length || 0} Siswa
                      </span>
                    </button>
                  )
                })}
            </div>
          </div>

          {/* Hub 2: Pilih Jam Pelajaran */}
          <div className="hub-section">
            <div className="hub-label">
              <span>⏰ 2. Jam Pelajaran (JP)</span>
              {selectedJadwalId !== 'semua' && (
                <button
                  type="button"
                  onClick={() => handleSelectJadwal('semua')}
                  style={{ background: 'transparent', border: 'none', color: 'var(--accent)', cursor: 'pointer', fontWeight: 700, fontSize: '0.7rem' }}
                >
                  Reset Semua
                </button>
              )}
            </div>
            <select
              className="form-select"
              value={selectedJadwalId}
              onChange={(e) => handleSelectJadwal(e.target.value)}
              style={{ width: '100%', fontSize: '0.825rem', fontWeight: 600, padding: '0.5rem 0.75rem', borderRadius: 'var(--radius-md)' }}
            >
              <option value="semua">
                Semua Jam Pelajaran ({filteredJadwalList.length} Sesi)
              </option>
              {filteredJadwalList.map((j) => {
                const isToday = j.hari?.toLowerCase() === new Date().toLocaleDateString('id-ID', { weekday: 'long' }).toLowerCase()
                return (
                  <option key={j.id} value={j.id}>
                    {j.hari.toUpperCase()} • {formatJamPelajaran(j.jam_mulai, j.jam_selesai)} {isToday ? '🌟 (Hari Ini)' : ''}
                  </option>
                )
              })}
            </select>
          </div>

          {/* Hub 3 (Tunggal Mode): Pilih Sesi Pertemuan */}
          {viewMode === 'tunggal' && (
            <div className="hub-section">
              <div className="hub-label">
                <span>📌 3. Sesi Pertemuan KBM</span>
                <button
                  type="button"
                  onClick={openNewMeetingModal}
                  style={{ background: 'transparent', border: 'none', color: 'var(--accent)', cursor: 'pointer', fontWeight: 700, fontSize: '0.7rem' }}
                  title="Buka Pertemuan Baru"
                >
                  ➕ Buka Baru
                </button>
              </div>
              {filteredPertemuanList.length === 0 ? (
                <div style={{ color: 'var(--muted)', fontSize: '0.8rem', padding: '0.35rem 0' }}>
                  Belum ada sesi di kelas ini.
                </div>
              ) : (
                <select
                  className="form-select"
                  value={selectedPertemuanId}
                  onChange={(e) => handleSelectPertemuan(e.target.value)}
                  style={{ width: '100%', fontSize: '0.825rem', fontWeight: 700, padding: '0.5rem 0.75rem', borderRadius: 'var(--radius-md)' }}
                >
                  {filteredPertemuanList.map((p) => {
                    const isToday = p.tanggal === '2026-09-08'
                    return (
                      <option key={p.id} value={p.id}>
                        P{p.pertemuan_ke}: {p.topik} — {p.tanggal} {isToday ? '🌟 (Hari Ini)' : ''}
                      </option>
                    )
                  })}
                </select>
              )}
            </div>
          )}
        </div>

        {/* Sesi KBM Terpilih Ribbon */}
        {viewMode === 'tunggal' && selectedPertemuan && (
          <div
            style={{
              marginTop: '1rem',
              padding: '0.75rem 1.15rem',
              background: 'var(--card-subtle)',
              borderRadius: 'var(--radius-md)',
              border: '1px solid var(--border)',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'space-between',
              flexWrap: 'wrap',
              gap: '0.75rem',
              fontSize: '0.825rem'
            }}
          >
            <div style={{ display: 'flex', alignItems: 'center', gap: '1.25rem', flexWrap: 'wrap' }}>
              <span>
                🏫 Kelas: <strong>{selectedPertemuan.jadwal?.kelas?.nama || 'Kelas PPLG'}</strong>
              </span>
              <span>
                ⏰ Sesi: <strong style={{ color: 'var(--accent)' }}>{formatJamPelajaran(selectedPertemuan.jadwal?.jam_mulai, selectedPertemuan.jadwal?.jam_selesai)}</strong>
              </span>
              <span>
                📅 Tanggal: <strong>{selectedPertemuan.jadwal?.hari?.toUpperCase()}, {selectedPertemuan.tanggal}</strong>
              </span>
              <span>
                📖 Topik: <strong>P{selectedPertemuan.pertemuan_ke}: {selectedPertemuan.topik}</strong>
              </span>
            </div>
            <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
              <span className="badge badge-accent">Guru Pengampu: Bu Yayu</span>
            </div>
          </div>
        )}
      </div>

      {/* =========================================================================
          MODE 1: INPUT & REKAP PER PERTEMUAN TUNGGAL
          ========================================================================= */}
      {viewMode === 'tunggal' && (
        <>
          {/* Action & KPI Health Command Bar */}
          <div
            style={{
              background: 'var(--card)',
              border: '1px solid var(--border)',
              borderRadius: 'var(--radius-lg)',
              padding: '1.25rem 1.5rem',
              marginBottom: '1.5rem',
              boxShadow: 'var(--shadow-sm)'
            }}
          >
            {/* Top Row: Score on left, Quick Action Toolbar on right */}
            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '1.25rem', marginBottom: '1.15rem' }}>
              {/* Left: Overall Health Score */}
              <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                <div
                  style={{
                    width: '56px',
                    height: '56px',
                    borderRadius: '50%',
                    background: persentaseHadir >= 80 ? '#ecfdf5' : '#fffbeb',
                    border: `2.5px solid ${persentaseHadir >= 80 ? '#10b981' : '#f59e0b'}`,
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontWeight: 900,
                    fontSize: '1.15rem',
                    color: persentaseHadir >= 80 ? '#047857' : '#b45309',
                    boxShadow: persentaseHadir >= 80 ? '0 4px 12px rgba(16, 185, 129, 0.2)' : 'none'
                  }}
                >
                  {persentaseHadir}%
                </div>
                <div>
                  <div style={{ fontWeight: 800, fontSize: '1.05rem', color: 'var(--text)' }}>
                    Tingkat Kehadiran Sah: {persentaseHadir}%
                  </div>
                  <div style={{ fontSize: '0.8rem', color: 'var(--muted)' }}>
                    {hadirCount + dispenCount} dari {totalSiswa} siswa berstatus Hadir / Dispensasi sah pada sesi ini
                  </div>
                </div>
              </div>

              {/* Right: Quick Actions & Primary Save CTA */}
              <div style={{ display: 'flex', alignItems: 'center', gap: '0.65rem', flexWrap: 'wrap' }}>
                <Button variant="secondary" size="sm" icon="⚡" onClick={handleMarkAllHadir} title="Tandai seluruh siswa sebagai Hadir">
                  Semua Hadir
                </Button>
                <Button variant="outline" size="sm" icon="⚠️" onClick={handleMarkUnmarkedAlpa} title="Tandai yang belum diabsen sebagai Alpa">
                  Kosongkan
                </Button>
                <Button
                  variant="primary"
                  size="md"
                  icon="💾"
                  loading={saving}
                  disabled={!selectedPertemuanId}
                  onClick={handleSaveAbsensi}
                  style={{ fontWeight: 800, padding: '0.6rem 1.35rem' }}
                >
                  Simpan Presensi Sesi Ini
                </Button>
              </div>
            </div>

            {/* Middle Row: Clickable Status Filter Badges */}
            <div style={{ display: 'flex', gap: '0.45rem', flexWrap: 'wrap', marginBottom: '0.85rem' }}>
              <button
                type="button"
                onClick={() => setStatusFilter('semua')}
                className={`btn btn-sm ${statusFilter === 'semua' ? 'btn-primary' : 'btn-secondary'}`}
                style={{ borderRadius: 'var(--radius-full)', fontWeight: 700, fontSize: '0.78rem' }}
              >
                Semua ({totalSiswa})
              </button>
              <button
                type="button"
                onClick={() => setStatusFilter('hadir')}
                style={{
                  padding: '0.35rem 0.75rem',
                  borderRadius: 'var(--radius-full)',
                  fontSize: '0.78rem',
                  fontWeight: 700,
                  border: '1.5px solid #a7f3d0',
                  background: statusFilter === 'hadir' ? '#10b981' : '#ecfdf5',
                  color: statusFilter === 'hadir' ? '#ffffff' : '#047857',
                  cursor: 'pointer',
                  transition: 'var(--transition)'
                }}
              >
                ✅ Hadir: {hadirCount}
              </button>
              <button
                type="button"
                onClick={() => setStatusFilter('dispen')}
                style={{
                  padding: '0.35rem 0.75rem',
                  borderRadius: 'var(--radius-full)',
                  fontSize: '0.78rem',
                  fontWeight: 700,
                  border: '1.5px solid #ddd6fe',
                  background: statusFilter === 'dispen' ? '#8b5cf6' : '#f5f3ff',
                  color: statusFilter === 'dispen' ? '#ffffff' : '#6d28d9',
                  cursor: 'pointer',
                  transition: 'var(--transition)'
                }}
              >
                🏷️ Dispen: {dispenCount}
              </button>
              <button
                type="button"
                onClick={() => setStatusFilter('izin')}
                style={{
                  padding: '0.35rem 0.75rem',
                  borderRadius: 'var(--radius-full)',
                  fontSize: '0.78rem',
                  fontWeight: 700,
                  border: '1.5px solid #bfdbfe',
                  background: statusFilter === 'izin' ? '#3b82f6' : '#eff6ff',
                  color: statusFilter === 'izin' ? '#ffffff' : '#1d4ed8',
                  cursor: 'pointer',
                  transition: 'var(--transition)'
                }}
              >
                ℹ️ Izin: {izinCount}
              </button>
              <button
                type="button"
                onClick={() => setStatusFilter('sakit')}
                style={{
                  padding: '0.35rem 0.75rem',
                  borderRadius: 'var(--radius-full)',
                  fontSize: '0.78rem',
                  fontWeight: 700,
                  border: '1.5px solid #fde68a',
                  background: statusFilter === 'sakit' ? '#f59e0b' : '#fffbeb',
                  color: statusFilter === 'sakit' ? '#ffffff' : '#b45309',
                  cursor: 'pointer',
                  transition: 'var(--transition)'
                }}
              >
                🏥 Sakit: {sakitCount}
              </button>
              <button
                type="button"
                onClick={() => setStatusFilter('alpa')}
                style={{
                  padding: '0.35rem 0.75rem',
                  borderRadius: 'var(--radius-full)',
                  fontSize: '0.78rem',
                  fontWeight: 700,
                  border: '1.5px solid #fecaca',
                  background: statusFilter === 'alpa' ? '#ef4444' : '#fef2f2',
                  color: statusFilter === 'alpa' ? '#ffffff' : '#b91c1c',
                  cursor: 'pointer',
                  transition: 'var(--transition)'
                }}
              >
                ❌ Alpa: {alpaCount}
              </button>
            </div>

            {/* Bottom Row: Stacked Multi-color Distribution Bar */}
            <div className="attendance-progress-bar">
              <div style={{ width: `${totalSiswa > 0 ? (hadirCount / totalSiswa) * 100 : 0}%`, background: '#10b981' }} className="progress-segment" title={`Hadir: ${hadirCount}`} />
              <div style={{ width: `${totalSiswa > 0 ? (dispenCount / totalSiswa) * 100 : 0}%`, background: '#8b5cf6' }} className="progress-segment" title={`Dispen: ${dispenCount}`} />
              <div style={{ width: `${totalSiswa > 0 ? (izinCount / totalSiswa) * 100 : 0}%`, background: '#3b82f6' }} className="progress-segment" title={`Izin: ${izinCount}`} />
              <div style={{ width: `${totalSiswa > 0 ? (sakitCount / totalSiswa) * 100 : 0}%`, background: '#f59e0b' }} className="progress-segment" title={`Sakit: ${sakitCount}`} />
              <div style={{ width: `${totalSiswa > 0 ? (alpaCount / totalSiswa) * 100 : 0}%`, background: '#ef4444' }} className="progress-segment" title={`Alpa: ${alpaCount}`} />
            </div>
          </div>

          {/* Search & Filter Toolbar */}
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: '0.75rem', marginBottom: '1rem' }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', flex: 1, minWidth: '240px' }}>
              <input
                type="text"
                placeholder="🔍 Cari nama siswa atau NISN..."
                className="form-control"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                style={{ maxWidth: '360px', width: '100%', fontSize: '0.85rem' }}
              />
            </div>
            <span style={{ fontSize: '0.8rem', color: 'var(--muted)' }}>
              Menampilkan {filteredSiswaList.length} dari {totalSiswa} siswa di kelas ini
            </span>
          </div>

          {/* Table Daftar Siswa Single Mode */}
          <Card>
            {loading ? (
              <div style={{ padding: '2.5rem', textAlign: 'center', color: 'var(--muted)' }}>
                Memuat data presensi sesi...
              </div>
            ) : filteredSiswaList.length === 0 ? (
              <div className="empty-state" style={{ padding: '2.5rem', textAlign: 'center' }}>
                <p className="text-muted">Tidak ada siswa yang sesuai dengan filter pencarian.</p>
              </div>
            ) : (
              <div className="table-responsive">
                <table className="table" style={{ verticalAlign: 'middle' }}>
                  <thead>
                    <tr>
                      <th style={{ width: '45px', textAlign: 'center' }}>No</th>
                      <th className="table-sticky-col-name" style={{ minWidth: '220px' }}>Identitas Siswa</th>
                      <th style={{ width: '380px', textAlign: 'center' }}>Status Kehadiran</th>
                      <th>Catatan / Alasan Dispensasi & Izin</th>
                    </tr>
                  </thead>
                  <tbody>
                    {filteredSiswaList.map((s, idx) => {
                      const cur = absensiMap[s.id] || { status: 'hadir', keterangan: '' }
                      const initials = s.name ? s.name.split(' ').map(p => p[0]).slice(0, 2).join('').toUpperCase() : 'SW'
                      const avatarGradients = [
                        'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                        'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
                        'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)',
                        'linear-gradient(135deg, #ec4899 0%, #db2777 100%)',
                        'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                        'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)'
                      ]
                      const gradient = avatarGradients[idx % avatarGradients.length]

                      return (
                        <tr key={s.id}>
                          <td style={{ textAlign: 'center', fontWeight: 700, color: 'var(--muted)' }}>
                            {idx + 1}
                          </td>
                          <td className="table-sticky-col-name">
                            <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem' }}>
                              <div className="avatar-initials" style={{ background: gradient }}>
                                {initials}
                              </div>
                              <div>
                                <div style={{ fontWeight: 800, color: 'var(--text)', fontSize: '0.925rem' }}>
                                  {s.name}
                                </div>
                                <div style={{ fontSize: '0.75rem', color: 'var(--muted)', display: 'flex', alignItems: 'center', gap: '0.35rem' }}>
                                  <span>{s.email}</span>
                                  <span style={{ opacity: 0.4 }}>•</span>
                                  <span className="badge badge-neutral" style={{ fontSize: '0.65rem', padding: '0.05rem 0.35rem' }}>
                                    NISN 00{s.id}82
                                  </span>
                                </div>
                              </div>
                            </div>
                          </td>
                          <td style={{ textAlign: 'center' }}>
                            <div className="status-pill-group" style={{ justifyContent: 'center' }}>
                              <button
                                type="button"
                                className={`status-choice-btn ${cur.status === 'hadir' ? 'active-hadir' : ''}`}
                                onClick={() => handleSetStatus(s.id, 'hadir')}
                                title="Hadir tepat waktu"
                              >
                                <span>✅</span>
                                <span>Hadir</span>
                              </button>
                              <button
                                type="button"
                                className={`status-choice-btn ${cur.status === 'dispen' ? 'active-dispen' : ''}`}
                                onClick={() => handleSetStatus(s.id, 'dispen')}
                                title="Dispensasi dinas / lomba sekolah"
                              >
                                <span>🏷️</span>
                                <span>Dispen</span>
                              </button>
                              <button
                                type="button"
                                className={`status-choice-btn ${cur.status === 'izin' ? 'active-izin' : ''}`}
                                onClick={() => handleSetStatus(s.id, 'izin')}
                                title="Izin kepentingan keluarga"
                              >
                                <span>ℹ️</span>
                                <span>Izin</span>
                              </button>
                              <button
                                type="button"
                                className={`status-choice-btn ${cur.status === 'sakit' ? 'active-sakit' : ''}`}
                                onClick={() => handleSetStatus(s.id, 'sakit')}
                                title="Sakit dengan/tanpa surat dokter"
                              >
                                <span>🏥</span>
                                <span>Sakit</span>
                              </button>
                              <button
                                type="button"
                                className={`status-choice-btn ${cur.status === 'alpa' ? 'active-alpa' : ''}`}
                                onClick={() => handleSetStatus(s.id, 'alpa')}
                                title="Tidak hadir tanpa keterangan"
                              >
                                <span>❌</span>
                                <span>Alpa</span>
                              </button>
                            </div>
                          </td>
                          <td>
                            <div style={{ display: 'flex', flexDirection: 'column', gap: '0.35rem' }}>
                              <input
                                type="text"
                                className="form-control form-control-sm"
                                placeholder={
                                  cur.status === 'dispen' ? 'Alasan dispensasi dinas / lomba...' :
                                  cur.status === 'sakit' ? 'Keterangan sakit / surat dokter...' :
                                  cur.status === 'izin' ? 'Keterangan izin keluarga / acara...' :
                                  cur.status === 'alpa' ? 'Catatan alpa / tindak lanjut wali kelas...' :
                                  'Catatan kehadiran (opsional)...'
                                }
                                value={cur.keterangan || ''}
                                onChange={(e) => handleSetKeterangan(s.id, e.target.value)}
                                style={{ fontSize: '0.825rem', padding: '0.4rem 0.65rem' }}
                              />
                              {cur.status === 'dispen' && (
                                <div style={{ display: 'flex', gap: '0.3rem', flexWrap: 'wrap' }}>
                                  <span className="reason-chip" onClick={() => handleSetKeterangan(s.id, 'Dispensasi persiapan kejuaraan LKS')}>+ LKS Nasional</span>
                                  <span className="reason-chip" onClick={() => handleSetKeterangan(s.id, 'Dispensasi tugas dinas sekolah')}>+ Tugas Dinas</span>
                                  <span className="reason-chip" onClick={() => handleSetKeterangan(s.id, 'Dispensasi pameran karya kejuruan')}>+ Pameran Kejuruan</span>
                                </div>
                              )}
                              {cur.status === 'sakit' && (
                                <div style={{ display: 'flex', gap: '0.3rem', flexWrap: 'wrap' }}>
                                  <span className="reason-chip" onClick={() => handleSetKeterangan(s.id, 'Sakit demam & flu, ada surat dokter')}>+ Surat Dokter</span>
                                  <span className="reason-chip" onClick={() => handleSetKeterangan(s.id, 'Sakit izin orang tua tanpa surat')}>+ Izin Ortu</span>
                                  <span className="reason-chip" onClick={() => handleSetKeterangan(s.id, 'Rawat jalan puskesmas')}>+ Rawat Jalan</span>
                                </div>
                              )}
                              {cur.status === 'izin' && (
                                <div style={{ display: 'flex', gap: '0.3rem', flexWrap: 'wrap' }}>
                                  <span className="reason-chip" onClick={() => handleSetKeterangan(s.id, 'Izin keperluan keluarga mendesak')}>+ Acara Keluarga</span>
                                  <span className="reason-chip" onClick={() => handleSetKeterangan(s.id, 'Izin kepentingan pribadi surat ortu')}>+ Ada Surat</span>
                                </div>
                              )}
                            </div>
                          </td>
                        </tr>
                      )
                    })}
                  </tbody>
                </table>
              </div>
            )}
          </Card>
        </>
      )}

      {/* =========================================================================
          MODE 2: INPUT & REKAP MATRIKS MULTI-PERTEMUAN (SEMUA PERTEMUAN)
          ========================================================================= */}
      {viewMode === 'matriks' && (
        <div>
          {/* Matrix Controls & Bulk Action Bar */}
          <Card className="mb-4">
            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '1rem' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: '0.85rem', flexWrap: 'wrap', flex: 1 }}>
                <div style={{ fontWeight: 700, fontSize: '0.875rem' }}>Aksi Cepat Multi-Pertemuan:</div>
                <select
                  className="form-select"
                  value={targetBatchPertemuanId}
                  onChange={(e) => setTargetBatchPertemuanId(e.target.value)}
                  style={{ maxWidth: '300px', fontSize: '0.85rem' }}
                >
                  {(matrixData?.pertemuan || []).map((p) => (
                    <option key={p.id} value={p.id}>
                      P{p.pertemuan_ke}: {p.topik} ({p.tanggal})
                    </option>
                  ))}
                </select>
                <Button variant="secondary" size="sm" icon="⚡" onClick={handleBatchMarkMeetingHadir}>
                  Tandai Semua Hadir di P ini
                </Button>
                <Button variant="outline" size="sm" icon="➕" onClick={openNewMeetingModal} title="Buka Pertemuan Baru di Kelas Ini">
                  Buka Pertemuan Baru
                </Button>
              </div>

              <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem' }}>
                <Button
                  variant="primary"
                  size="md"
                  icon="💾"
                  loading={savingMatrix}
                  onClick={handleSaveBatchMatrix}
                >
                  Simpan Perubahan Seluruh Pertemuan
                </Button>
              </div>
            </div>

            <div style={{ marginTop: '0.85rem', padding: '0.65rem 0.85rem', background: 'var(--card-subtle)', borderRadius: 'var(--radius-sm)', border: '1px solid var(--border)', fontSize: '0.8rem', color: 'var(--text-light)', display: 'flex', alignItems: 'center', gap: '0.5rem', flexWrap: 'wrap' }}>
              <span>💡 <b>Tips Pengisian Cepat:</b> Klik langsung pada kotak status (H/I/S/D/A) di tabel untuk mengganti status kehadiran secara instan, lalu klik <b>Simpan Perubahan Seluruh Pertemuan</b>.</span>
              <div style={{ display: 'flex', gap: '0.5rem', marginLeft: 'auto' }}>
                <span style={{ color: '#047857', fontWeight: 700 }}>H = Hadir</span>
                <span style={{ color: '#1d4ed8', fontWeight: 700 }}>I = Izin</span>
                <span style={{ color: '#b45309', fontWeight: 700 }}>S = Sakit</span>
                <span style={{ color: '#6d28d9', fontWeight: 700 }}>D = Dispen</span>
                <span style={{ color: '#b91c1c', fontWeight: 700 }}>A = Alpa</span>
              </div>
            </div>
          </Card>

          {/* Search Bar for Matrix */}
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: '0.75rem', marginBottom: '1rem' }}>
            <input
              type="text"
              placeholder="🔍 Cari nama siswa atau NISN di matriks..."
              className="form-control"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              style={{ maxWidth: '360px', fontSize: '0.85rem' }}
            />
            <span style={{ fontSize: '0.8rem', color: 'var(--muted)' }}>
              Menampilkan {filteredRekapSiswa.length} siswa • {(matrixData?.pertemuan || []).length} sesi pertemuan
            </span>
          </div>

          {/* Matrix Spreadsheet Grid */}
          <Card>
            {loadingMatrix ? (
              <div style={{ padding: '3rem', textAlign: 'center', color: 'var(--muted)' }}>
                Memuat matriks presensi multi-pertemuan...
              </div>
            ) : !matrixData ? (
              <div className="empty-state" style={{ padding: '2rem' }}>
                <p className="text-muted">Data matriks belum dapat dimuat.</p>
              </div>
            ) : (
              <div className="table-responsive" style={{ overflowX: 'auto' }}>
                <table className="table" style={{ fontSize: '0.825rem', verticalAlign: 'middle' }}>
                  <thead>
                    <tr>
                      <th style={{ width: '40px', textAlign: 'center' }}>No</th>
                      <th className="table-sticky-col-name" style={{ minWidth: '220px' }}>Nama Siswa</th>
                      {(matrixData.pertemuan || []).map((p) => (
                        <th key={p.id} style={{ textAlign: 'center', minWidth: '76px', padding: '0.5rem 0.25rem' }} title={`Pertemuan ${p.pertemuan_ke}: ${p.topik} (${p.tanggal})`}>
                          <div style={{ fontWeight: 800 }}>P{p.pertemuan_ke}</div>
                          <div style={{ fontSize: '0.675rem', fontWeight: 'normal', color: 'var(--muted)', marginBottom: '0.35rem' }}>
                            {p.tanggal ? p.tanggal.slice(5) : ''}
                          </div>
                          <button
                            type="button"
                            onClick={() => handleMarkAllHadirForMeeting(p.id)}
                            title={`Tandai seluruh siswa Hadir di Pertemuan ${p.pertemuan_ke}`}
                            style={{
                              fontSize: '0.625rem',
                              padding: '0.15rem 0.35rem',
                              borderRadius: 'var(--radius-sm)',
                              background: 'rgba(16, 185, 129, 0.12)',
                              color: '#047857',
                              border: '1px solid #a7f3d0',
                              cursor: 'pointer',
                              fontWeight: 700,
                              whiteSpace: 'nowrap'
                            }}
                          >
                            ⚡ Semua H
                          </button>
                        </th>
                      ))}
                      <th style={{ width: '45px', textAlign: 'center', color: '#047857' }} title="Total Hadir">H</th>
                      <th style={{ width: '45px', textAlign: 'center', color: '#1d4ed8' }} title="Total Izin">I</th>
                      <th style={{ width: '45px', textAlign: 'center', color: '#b45309' }} title="Total Sakit">S</th>
                      <th style={{ width: '45px', textAlign: 'center', color: '#6d28d9' }} title="Total Dispen">D</th>
                      <th style={{ width: '45px', textAlign: 'center', color: '#b91c1c' }} title="Total Alpa">A</th>
                      <th style={{ minWidth: '85px', textAlign: 'center' }}>% Sah</th>
                    </tr>
                  </thead>
                  <tbody>
                    {filteredRekapSiswa.map((r, idx) => {
                      const sId = r.siswa?.id
                      const draftRow = matrixDraft[sId] || {}
                      
                      // Hitung statistik real-time dari draft lokal
                      let h = 0, i = 0, sk = 0, d = 0, a = 0
                      ;(matrixData.pertemuan || []).forEach((p) => {
                        const st = draftRow[p.id]?.status
                        if (st === 'hadir') h++
                        else if (st === 'izin') i++
                        else if (st === 'sakit') sk++
                        else if (st === 'dispen') d++
                        else if (st === 'alpa') a++
                      })
                      const totalP = (matrixData.pertemuan || []).length
                      const persentase = totalP > 0 ? Math.round(((h + d) / totalP) * 100) : 0

                      const initials = r.siswa?.name ? r.siswa.name.split(' ').map(p => p[0]).slice(0, 2).join('').toUpperCase() : 'SW'
                      const avatarGradients = [
                        'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                        'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
                        'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)',
                        'linear-gradient(135deg, #ec4899 0%, #db2777 100%)',
                        'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                        'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)'
                      ]
                      const gradient = avatarGradients[idx % avatarGradients.length]

                      return (
                        <tr key={sId || idx}>
                          <td style={{ textAlign: 'center', fontWeight: 700, color: 'var(--muted)' }}>{idx + 1}</td>
                          <td className="table-sticky-col-name">
                            <div style={{ display: 'flex', alignItems: 'center', gap: '0.65rem' }}>
                              <div className="avatar-initials" style={{ width: '32px', height: '32px', fontSize: '0.75rem', background: gradient }}>
                                {initials}
                              </div>
                              <div>
                                <div style={{ fontWeight: 800, color: 'var(--text)', fontSize: '0.875rem' }}>
                                  {r.siswa?.name || 'Siswa'}
                                </div>
                                <div style={{ fontSize: '0.7rem', color: 'var(--muted)', display: 'flex', alignItems: 'center', gap: '0.35rem' }}>
                                  <span>{r.siswa?.email}</span>
                                  <span style={{ opacity: 0.4 }}>•</span>
                                  <span className="badge badge-neutral" style={{ fontSize: '0.625rem', padding: '0.05rem 0.35rem' }}>
                                    NISN 00{sId}82
                                  </span>
                                </div>
                              </div>
                            </div>
                          </td>

                          {/* Meeting Cells with Interactive Click */}
                          {(matrixData.pertemuan || []).map((p) => {
                            const curStatus = draftRow[p.id]?.status || 'hadir'
                            const originalStatus = matrixData.matrix?.[sId]?.[p.id]?.status || 'hadir'
                            const isModified = originalStatus !== curStatus
                            const cfg = statusBadges[curStatus] || statusBadges.hadir
                            const symbol = {
                              hadir: 'H',
                              izin: 'I',
                              sakit: 'S',
                              dispen: 'D',
                              alpa: 'A'
                            }[curStatus] || 'H'

                            return (
                              <td key={p.id} style={{ textAlign: 'center', padding: '0.35rem' }}>
                                <button
                                  type="button"
                                  onClick={() => handleCellClick(sId, p.id)}
                                  title={`P${p.pertemuan_ke}: ${cfg.label} (${isModified ? 'Telah diubah - belum disimpan' : 'Klik untuk ganti status'})`}
                                  style={{
                                    width: '34px',
                                    height: '34px',
                                    borderRadius: 'var(--radius-sm)',
                                    fontSize: '0.85rem',
                                    fontWeight: 800,
                                    border: isModified ? '2px solid #f59e0b' : `1px solid ${cfg.border}`,
                                    background: cfg.bg,
                                    color: cfg.color,
                                    cursor: 'pointer',
                                    transition: 'all 0.15s ease',
                                    display: 'inline-flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    position: 'relative',
                                    boxShadow: isModified ? '0 0 0 2px rgba(245, 158, 11, 0.3)' : 'none'
                                  }}
                                  onMouseDown={(e) => e.currentTarget.style.transform = 'scale(0.92)'}
                                  onMouseUp={(e) => e.currentTarget.style.transform = 'scale(1)'}
                                >
                                  {symbol}
                                  {isModified && (
                                    <span
                                      style={{
                                        position: 'absolute',
                                        top: '-3px',
                                        right: '-3px',
                                        width: '8px',
                                        height: '8px',
                                        borderRadius: '50%',
                                        background: '#f59e0b',
                                        border: '1.5px solid #ffffff'
                                      }}
                                      title="Perubahan lokal belum disimpan"
                                    />
                                  )}
                                </button>
                              </td>
                            )
                          })}

                          {/* Summary Counters with Soft Colored Badges */}
                          <td style={{ textAlign: 'center' }}>
                            <span style={{ display: 'inline-block', minWidth: '24px', padding: '0.15rem 0.35rem', borderRadius: '4px', background: '#ecfdf5', color: '#047857', fontWeight: 800 }}>{h}</span>
                          </td>
                          <td style={{ textAlign: 'center' }}>
                            <span style={{ display: 'inline-block', minWidth: '24px', padding: '0.15rem 0.35rem', borderRadius: '4px', background: '#eff6ff', color: '#1d4ed8', fontWeight: 800 }}>{i}</span>
                          </td>
                          <td style={{ textAlign: 'center' }}>
                            <span style={{ display: 'inline-block', minWidth: '24px', padding: '0.15rem 0.35rem', borderRadius: '4px', background: '#fffbeb', color: '#b45309', fontWeight: 800 }}>{sk}</span>
                          </td>
                          <td style={{ textAlign: 'center' }}>
                            <span style={{ display: 'inline-block', minWidth: '24px', padding: '0.15rem 0.35rem', borderRadius: '4px', background: '#f5f3ff', color: '#6d28d9', fontWeight: 800 }}>{d}</span>
                          </td>
                          <td style={{ textAlign: 'center' }}>
                            <span style={{ display: 'inline-block', minWidth: '24px', padding: '0.15rem 0.35rem', borderRadius: '4px', background: '#fef2f2', color: '#b91c1c', fontWeight: 800 }}>{a}</span>
                          </td>
                          <td style={{ textAlign: 'center' }}>
                            <span
                              className={`badge ${persentase >= 80 ? 'badge-accent' : persentase >= 60 ? 'badge-warning' : 'badge-danger'}`}
                              style={{ fontWeight: 800, fontSize: '0.78rem', padding: '0.25rem 0.55rem' }}
                            >
                              {persentase}%
                            </span>
                          </td>
                        </tr>
                      )
                    })}
                  </tbody>
                </table>
              </div>
            )}
          </Card>

          {/* Floating / Sticky Unsaved Warning Bar */}
          {unsavedMatrixCount > 0 && (
            <div
              style={{
                position: 'sticky',
                bottom: '1.5rem',
                marginTop: '1rem',
                zIndex: 40,
                background: 'linear-gradient(135deg, #1e293b 0%, #0f172a 100%)',
                color: '#ffffff',
                padding: '0.85rem 1.25rem',
                borderRadius: 'var(--radius-lg)',
                boxShadow: '0 10px 25px -5px rgba(0,0,0,0.4), 0 0 15px rgba(245, 158, 11, 0.4)',
                border: '1px solid #f59e0b',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                flexWrap: 'wrap',
                gap: '1rem'
              }}
            >
              <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem' }}>
                <span style={{ fontSize: '1.35rem' }}>⚠️</span>
                <div>
                  <div style={{ fontWeight: 800, color: '#fbbf24', fontSize: '0.95rem' }}>
                    {unsavedMatrixCount} Perubahan Presensi Belum Disimpan
                  </div>
                  <div style={{ fontSize: '0.775rem', color: '#94a3b8' }}>
                    Tanda titik oranye menunjukkan sel yang baru diedit. Klik Simpan agar tersimpan ke rapor siswa.
                  </div>
                </div>
              </div>
              <div style={{ display: 'flex', alignItems: 'center', gap: '0.65rem' }}>
                <Button
                  variant="ghost"
                  size="sm"
                  onClick={loadMatrixData}
                  style={{ color: '#cbd5e1' }}
                  title="Batalkan perubahan dan kembalikan ke data server"
                >
                  Batal / Reset
                </Button>
                <Button
                  variant="primary"
                  size="md"
                  icon="💾"
                  loading={savingMatrix}
                  onClick={handleSaveBatchMatrix}
                  style={{ background: '#f59e0b', borderColor: '#d97706', color: '#0f172a', fontWeight: 800 }}
                >
                  Simpan Semua Perubahan
                </Button>
              </div>
            </div>
          )}
        </div>
      )}

      {/* Modal Buat Pertemuan Baru (Bu Yayu) */}
      {isNewMeetingModalOpen && (
        <div
          className="modal-overlay"
          style={{
            position: 'fixed',
            inset: 0,
            background: 'rgba(0, 0, 0, 0.65)',
            backdropFilter: 'blur(4px)',
            zIndex: 1000,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            padding: '1rem'
          }}
        >
          <div
            className="card"
            style={{
              maxWidth: '540px',
              width: '100%',
              borderRadius: 'var(--radius-lg)',
              boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.4)',
              maxHeight: '90vh',
              overflowY: 'auto',
              border: '1px solid var(--border)'
            }}
          >
            <div
              style={{
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
                marginBottom: '1.25rem',
                borderBottom: '1px solid var(--border)',
                paddingBottom: '0.75rem'
              }}
            >
              <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                <span style={{ fontSize: '1.35rem' }}>➕</span>
                <div>
                  <h3 style={{ fontSize: '1.15rem', fontWeight: 800, margin: 0 }}>
                    Buka Sesi Pertemuan Baru
                  </h3>
                  <div style={{ fontSize: '0.75rem', color: 'var(--muted)' }}>
                    Buat sesi KBM baru untuk kelas dan jam yang dipilih
                  </div>
                </div>
              </div>
              <button
                type="button"
                className="btn btn-ghost btn-sm"
                onClick={() => setIsNewMeetingModalOpen(false)}
                style={{ fontSize: '1.2rem', lineHeight: 1, padding: '0.25rem 0.5rem' }}
              >
                ✕
              </button>
            </div>

            <form onSubmit={handleCreateNewMeeting}>
              {/* Info Kelas */}
              <div style={{ marginBottom: '1rem' }}>
                <label className="form-label" style={{ fontWeight: 700, fontSize: '0.825rem' }}>
                  Kelas Target:
                </label>
                <div
                  style={{
                    padding: '0.6rem 0.85rem',
                    background: 'var(--card-subtle)',
                    borderRadius: 'var(--radius-md)',
                    border: '1px solid var(--border)',
                    fontWeight: 700,
                    color: 'var(--text)',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between'
                  }}
                >
                  <span>🏫 Kelas {kelasList.find((k) => String(k.id) === String(selectedKelasId))?.nama || 'XII PPLG 1'}</span>
                  <span className="badge badge-accent">Tahun 2026/2027</span>
                </div>
              </div>

              {/* Pilih Jadwal & Jam Pelajaran */}
              <div style={{ marginBottom: '1rem' }}>
                <label className="form-label" style={{ fontWeight: 700, fontSize: '0.825rem' }}>
                  Sesi Jadwal & Jam Pelajaran:
                </label>
                <select
                  className="form-select"
                  value={newMeetingForm.jadwal_id}
                  onChange={(e) => setNewMeetingForm({ ...newMeetingForm, jadwal_id: e.target.value })}
                  required
                  style={{ width: '100%', fontSize: '0.85rem', fontWeight: 600 }}
                >
                  <option value="">-- Pilih Sesi Jadwal / Jam Pelajaran --</option>
                  {(filteredJadwalList.length > 0 ? filteredJadwalList : jadwalList).map((j) => (
                    <option key={j.id} value={j.id}>
                      {j.hari.toUpperCase()} • {formatJamPelajaran(j.jam_mulai, j.jam_selesai)} — {j.mapel?.nama}
                    </option>
                  ))}
                </select>
              </div>

              {/* Grid Nomor Pertemuan & Tanggal */}
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.85rem', marginBottom: '1rem' }}>
                <div>
                  <label className="form-label" style={{ fontWeight: 700, fontSize: '0.825rem' }}>
                    Pertemuan Ke-:
                  </label>
                  <input
                    type="number"
                    min="1"
                    className="form-control"
                    value={newMeetingForm.pertemuan_ke}
                    onChange={(e) => setNewMeetingForm({ ...newMeetingForm, pertemuan_ke: parseInt(e.target.value) || 1 })}
                    required
                  />
                </div>
                <div>
                  <label className="form-label" style={{ fontWeight: 700, fontSize: '0.825rem' }}>
                    Tanggal (Tahun 2026):
                  </label>
                  <input
                    type="date"
                    className="form-control"
                    value={newMeetingForm.tanggal}
                    onChange={(e) => setNewMeetingForm({ ...newMeetingForm, tanggal: e.target.value })}
                    required
                  />
                </div>
              </div>

              {/* Topik Pembahasan */}
              <div style={{ marginBottom: '1rem' }}>
                <label className="form-label" style={{ fontWeight: 700, fontSize: '0.825rem' }}>
                  Topik Materi Pembahasan:
                </label>
                <input
                  type="text"
                  className="form-control"
                  placeholder="Misal: Unsur Intrinsik Cerpen, Surat Resmi, dll."
                  value={newMeetingForm.topik}
                  onChange={(e) => setNewMeetingForm({ ...newMeetingForm, topik: e.target.value })}
                  required
                />
              </div>

              {/* Catatan Sesi KBM */}
              <div style={{ marginBottom: '1.25rem' }}>
                <label className="form-label" style={{ fontWeight: 700, fontSize: '0.825rem' }}>
                  Catatan KBM (Opsional):
                </label>
                <textarea
                  className="form-control"
                  rows={2}
                  placeholder="Catatan pelaksanaan KBM..."
                  value={newMeetingForm.catatan}
                  onChange={(e) => setNewMeetingForm({ ...newMeetingForm, catatan: e.target.value })}
                />
              </div>

              {/* Action Buttons */}
              <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '0.5rem', borderTop: '1px solid var(--border)', paddingTop: '0.85rem' }}>
                <Button variant="secondary" type="button" onClick={() => setIsNewMeetingModalOpen(false)}>
                  Batal
                </Button>
                <Button variant="primary" type="submit" loading={creatingMeeting} icon="💾">
                  Buat Pertemuan & Mulai Absen
                </Button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  )
}
