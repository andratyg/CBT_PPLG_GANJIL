/**
 * Attendance Export Utilities for CBT PPLG
 * Supports:
 * 1. Excel (.xls XML/HTML format with cell styling, emerald header, borders)
 * 2. CSV (.csv with UTF-8 BOM for Microsoft Excel compatibility)
 * 3. Printable Report / PDF (clean window.print with official school header)
 */

export function exportToExcel({
  pertemuan,
  data = [], // [{ no, name, email, status, keterangan }]
  summary = { total: 0, hadir: 0, izin: 0, sakit: 0, dispen: 0, alpa: 0 }
}) {
  const mapelNama = pertemuan?.jadwal?.mapel?.nama || 'Mata Pelajaran PPLG'
  const kelasNama = pertemuan?.jadwal?.kelas?.nama || 'Kelas PPLG'
  const guruNama = pertemuan?.jadwal?.guru?.name || 'Guru Pengampu'
  const pertemuanKe = pertemuan?.pertemuan_ke || 1
  const tanggal = pertemuan?.tanggal || new Date().toISOString().split('T')[0]
  const topik = pertemuan?.topik || '-'

  const statusLabel = {
    hadir: 'Hadir',
    izin: 'Izin',
    sakit: 'Sakit',
    dispen: 'Dispensasi',
    alpa: 'Alpa'
  }

  let tableRows = data.map((row, idx) => `
    <tr>
      <td style="text-align: center; border: 1px solid #cbd5e1;">${idx + 1}</td>
      <td style="border: 1px solid #cbd5e1;"><b>${row.name || '-'}</b></td>
      <td style="border: 1px solid #cbd5e1;">${row.email || '-'}</td>
      <td style="text-align: center; border: 1px solid #cbd5e1; font-weight: bold;">
        ${statusLabel[row.status] || row.status || 'Hadir'}
      </td>
      <td style="border: 1px solid #cbd5e1;">${row.keterangan || '-'}</td>
    </tr>
  `).join('')

  const excelContent = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
      <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"/>
      <!--[if gte mso 9]>
      <xml>
        <x:ExcelWorkbook>
          <x:ExcelWorksheets>
            <x:ExcelWorksheet>
              <x:Name>Presensi Pertemuan ${pertemuanKe}</x:Name>
              <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
            </x:ExcelWorksheet>
          </x:ExcelWorksheets>
        </x:ExcelWorkbook>
      </xml>
      <![endif]-->
      <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11pt; }
        .title { font-size: 16pt; font-weight: bold; color: #0f172a; }
        .subtitle { font-size: 11pt; color: #475569; }
        .meta-table td { padding: 4px 8px; font-size: 10pt; }
        .summary-table td { padding: 6px 12px; font-size: 10pt; text-align: center; border: 1px solid #94a3b8; }
        th { background-color: #10b981; color: #ffffff; font-weight: bold; padding: 8px; border: 1px solid #059669; text-align: center; }
        td { padding: 6px 8px; }
      </style>
    </head>
    <body>
      <table>
        <tr><td colspan="5" class="title">SMK PPLG — LAPORAN PRESENSI KEHADIRAN SISWA</td></tr>
        <tr><td colspan="5" class="subtitle">Sistem CBT Pembelajaran & Administrasi Guru</td></tr>
        <tr><td></td></tr>
      </table>

      <table class="meta-table">
        <tr><td><b>Mata Pelajaran:</b></td><td>${mapelNama}</td><td></td><td><b>Pertemuan Ke:</b></td><td>${pertemuanKe}</td></tr>
        <tr><td><b>Kelas / Rombel:</b></td><td>${kelasNama}</td><td></td><td><b>Tanggal:</b></td><td>${tanggal}</td></tr>
        <tr><td><b>Guru Pengampu:</b></td><td>${guruNama}</td><td></td><td><b>Topik Materi:</b></td><td>${topik}</td></tr>
      </table>

      <br/>

      <table class="summary-table">
        <tr style="background-color: #f1f5f9; font-weight: bold;">
          <td>Total Siswa</td>
          <td style="color: #047857;">Hadir</td>
          <td style="color: #1d4ed8;">Izin</td>
          <td style="color: #b45309;">Sakit</td>
          <td style="color: #6d28d9;">Dispen</td>
          <td style="color: #b91c1c;">Alpa</td>
        </tr>
        <tr>
          <td><b>${summary.total || data.length}</b></td>
          <td><b>${summary.hadir || 0}</b></td>
          <td><b>${summary.izin || 0}</b></td>
          <td><b>${summary.sakit || 0}</b></td>
          <td><b>${summary.dispen || 0}</b></td>
          <td><b>${summary.alpa || 0}</b></td>
        </tr>
      </table>

      <br/>

      <table style="border-collapse: collapse; width: 100%;">
        <thead>
          <tr>
            <th style="width: 50px;">No</th>
            <th style="width: 250px;">Nama Siswa</th>
            <th style="width: 200px;">Email / Akun</th>
            <th style="width: 120px;">Status Kehadiran</th>
            <th style="width: 250px;">Keterangan</th>
          </tr>
        </thead>
        <tbody>
          ${tableRows}
        </tbody>
      </table>

      <br/><br/>
      <table>
        <tr>
          <td colspan="3"></td>
          <td colspan="2" style="text-align: center;">
            ${tanggal ? new Date(tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : ''}<br/>
            Guru Pengampu,<br/><br/><br/><br/>
            <b>${guruNama}</b>
          </td>
        </tr>
      </table>
    </body>
    </html>
  `

  const blob = new Blob([excelContent], { type: 'application/vnd.ms-excel;charset=utf-8' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `Presensi_${kelasNama.replace(/\s+/g, '_')}_P${pertemuanKe}_${tanggal}.xls`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

export function exportToCSV({
  pertemuan,
  data = []
}) {
  const mapelNama = pertemuan?.jadwal?.mapel?.nama || 'Mapel'
  const kelasNama = pertemuan?.jadwal?.kelas?.nama || 'Kelas'
  const pertemuanKe = pertemuan?.pertemuan_ke || 1
  const tanggal = pertemuan?.tanggal || new Date().toISOString().split('T')[0]

  const statusLabel = {
    hadir: 'Hadir',
    izin: 'Izin',
    sakit: 'Sakit',
    dispen: 'Dispensasi',
    alpa: 'Alpa'
  }

  const headers = ['No', 'Nama Siswa', 'Email', 'Kelas', 'Mata Pelajaran', 'Pertemuan Ke', 'Tanggal', 'Status Kehadiran', 'Keterangan']

  const csvRows = [
    headers.map((h) => `"${h}"`).join(',')
  ]

  data.forEach((row, idx) => {
    const values = [
      idx + 1,
      row.name || '',
      row.email || '',
      kelasNama,
      mapelNama,
      pertemuanKe,
      tanggal,
      statusLabel[row.status] || row.status || 'Hadir',
      row.keterangan || ''
    ]
    csvRows.push(values.map((v) => `"${String(v).replace(/"/g, '""')}"`).join(','))
  })

  // Add UTF-8 BOM so Excel opens accents and symbols without encoding errors
  const csvContent = '\uFEFF' + csvRows.join('\r\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `Presensi_${kelasNama.replace(/\s+/g, '_')}_P${pertemuanKe}_${tanggal}.csv`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

export function printAttendanceReport({
  pertemuan,
  data = [],
  summary = { total: 0, hadir: 0, izin: 0, sakit: 0, dispen: 0, alpa: 0 }
}) {
  const mapelNama = pertemuan?.jadwal?.mapel?.nama || 'Mata Pelajaran PPLG'
  const kelasNama = pertemuan?.jadwal?.kelas?.nama || 'Kelas PPLG'
  const guruNama = pertemuan?.jadwal?.guru?.name || 'Guru Pengampu'
  const pertemuanKe = pertemuan?.pertemuan_ke || 1
  const tanggal = pertemuan?.tanggal || new Date().toISOString().split('T')[0]
  const topik = pertemuan?.topik || '-'

  const statusBadge = {
    hadir: '<span style="background:#ecfdf5; color:#047857; padding:2px 8px; border-radius:4px; font-weight:bold; border:1px solid #a7f3d0;">Hadir</span>',
    izin: '<span style="background:#eff6ff; color:#1d4ed8; padding:2px 8px; border-radius:4px; font-weight:bold; border:1px solid #bfdbfe;">Izin</span>',
    sakit: '<span style="background:#fffbeb; color:#b45309; padding:2px 8px; border-radius:4px; font-weight:bold; border:1px solid #fde68a;">Sakit</span>',
    dispen: '<span style="background:#f5f3ff; color:#6d28d9; padding:2px 8px; border-radius:4px; font-weight:bold; border:1px solid #ddd6fe;">Dispensasi</span>',
    alpa: '<span style="background:#fef2f2; color:#b91c1c; padding:2px 8px; border-radius:4px; font-weight:bold; border:1px solid #fecaca;">Alpa</span>'
  }

  const rowsHtml = data.map((row, idx) => `
    <tr>
      <td style="text-align:center; padding: 6px 8px; border: 1px solid #cbd5e1;">${idx + 1}</td>
      <td style="padding: 6px 8px; border: 1px solid #cbd5e1;"><b>${row.name}</b><br/><small style="color:#64748b">${row.email}</small></td>
      <td style="text-align:center; padding: 6px 8px; border: 1px solid #cbd5e1;">${statusBadge[row.status] || row.status}</td>
      <td style="padding: 6px 8px; border: 1px solid #cbd5e1; font-size: 9pt;">${row.keterangan || '—'}</td>
    </tr>
  `).join('')

  const printWindow = window.open('', '_blank', 'width=900,height=700')
  if (!printWindow) {
    alert('Harap izinkan popup browser untuk mencetak laporan.')
    return
  }

  printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Laporan Presensi ${kelasNama} - P${pertemuanKe}</title>
      <style>
        @page { size: A4 portrait; margin: 1.5cm; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #0f172a; margin: 0; font-size: 10pt; }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 16px; }
        .title { font-size: 16pt; font-weight: 800; letter-spacing: -0.5px; }
        .subtitle { font-size: 9pt; color: #475569; margin-top: 2px; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px; font-size: 9.5pt; }
        .summary-bar { display: flex; gap: 8px; margin-bottom: 16px; }
        .summary-box { flex: 1; text-align: center; padding: 6px; border-radius: 4px; border: 1px solid #cbd5e1; font-size: 9pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { background: #0f172a; color: #ffffff; padding: 8px; text-align: left; font-size: 9pt; }
        .signatures { display: flex; justify-content: space-between; margin-top: 36px; }
        .sig-box { text-align: center; width: 220px; }
        @media print {
          .no-print { display: none; }
        }
      </style>
    </head>
    <body>
      <div class="no-print" style="background:#f1f5f9; padding: 10px; margin-bottom: 15px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
        <span>Pratinjau Cetak / PDF Laporan Presensi Siswa</span>
        <button onclick="window.print()" style="background:#10b981; color:white; border:none; padding:6px 14px; border-radius:4px; font-weight:bold; cursor:pointer;">🖨️ Cetak / Simpan PDF Sekarang</button>
      </div>

      <div class="header">
        <div>
          <div class="title">SMK PPLG — DAFTAR PRESENSI SISWA</div>
          <div class="subtitle">Sistem CBT Pembelajaran Kejuruan & Administrasi Guru • Tahun Ajaran 2026/2027</div>
        </div>
      </div>

      <div class="meta-grid">
        <div>
          <div><strong>Mata Pelajaran:</strong> ${mapelNama}</div>
          <div><strong>Kelas / Rombel:</strong> ${kelasNama}</div>
          <div><strong>Topik:</strong> ${topik}</div>
        </div>
        <div style="text-align: right;">
          <div><strong>Pertemuan Ke:</strong> ${pertemuanKe}</div>
          <div><strong>Tanggal Sesi:</strong> ${tanggal}</div>
          <div><strong>Guru Pengampu:</strong> ${guruNama}</div>
        </div>
      </div>

      <div class="summary-bar">
        <div class="summary-box" style="background: #f8fafc;"><strong>Total:</strong> ${summary.total || data.length}</div>
        <div class="summary-box" style="background: #ecfdf5; color: #047857;"><strong>Hadir:</strong> ${summary.hadir || 0}</div>
        <div class="summary-box" style="background: #eff6ff; color: #1d4ed8;"><strong>Izin:</strong> ${summary.izin || 0}</div>
        <div class="summary-box" style="background: #fffbeb; color: #b45309;"><strong>Sakit:</strong> ${summary.sakit || 0}</div>
        <div class="summary-box" style="background: #f5f3ff; color: #6d28d9;"><strong>Dispen:</strong> ${summary.dispen || 0}</div>
        <div class="summary-box" style="background: #fef2f2; color: #b91c1c;"><strong>Alpa:</strong> ${summary.alpa || 0}</div>
      </div>

      <table>
        <thead>
          <tr>
            <th style="width: 40px; text-align: center;">No</th>
            <th>Nama Siswa & Akun</th>
            <th style="width: 110px; text-align: center;">Status</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          ${rowsHtml}
        </tbody>
      </table>

      <div class="signatures">
        <div class="sig-box">
          Mengetahui,<br/>
          Wali Kelas / Ketua Program<br/><br/><br/><br/>
          ( _________________________ )
        </div>
        <div class="sig-box">
          Bandung, ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}<br/>
          Guru Pengampu Mata Pelajaran<br/><br/><br/><br/>
          <strong>${guruNama}</strong>
        </div>
      </div>

      <script>
        window.onload = function() {
          setTimeout(function() { window.print(); }, 400);
        };
      </script>
    </body>
    </html>
  `)
  printWindow.document.close()
}

/**
 * Ekspor Rekap Matriks Multi-Pertemuan ke Excel Spreadsheet (.xls)
 */
export function exportMatrixToExcel({
  pertemuanList = [],
  rekapSiswa = [],
  kelasNama = 'Kelas XII PPLG',
  mapelNama = 'Bahasa Indonesia',
  guruNama = 'Bu Yayu'
}) {
  const meetingHeaders = pertemuanList.map((p) => `
    <th style="background-color: #047857; color: #ffffff; border: 1px solid #cbd5e1; padding: 6px; font-size: 9pt; text-align: center;">
      P${p.pertemuan_ke}<br/>
      <span style="font-size: 7.5pt; font-weight: normal;">${p.tanggal || ''}</span>
    </th>
  `).join('')

  const statusSymbol = {
    hadir: '<span style="color: #047857; font-weight: bold;">H</span>',
    izin: '<span style="color: #1d4ed8; font-weight: bold;">I</span>',
    sakit: '<span style="color: #b45309; font-weight: bold;">S</span>',
    dispen: '<span style="color: #6d28d9; font-weight: bold;">D</span>',
    alpa: '<span style="color: #b91c1c; font-weight: bold;">A</span>'
  }

  const rowsHtml = rekapSiswa.map((r, idx) => {
    const meetingCells = pertemuanList.map((p) => {
      const st = r.kehadiran?.[p.id]?.status
      return `<td style="text-align: center; border: 1px solid #cbd5e1; font-weight: bold;">${statusSymbol[st] || '—'}</td>`
    }).join('')

    return `
      <tr>
        <td style="text-align: center; border: 1px solid #cbd5e1;">${idx + 1}</td>
        <td style="border: 1px solid #cbd5e1;"><b>${r.siswa?.name || 'Siswa'}</b></td>
        <td style="border: 1px solid #cbd5e1; font-size: 9pt; color: #475569;">${r.siswa?.email || '-'}</td>
        ${meetingCells}
        <td style="text-align: center; border: 1px solid #cbd5e1; background: #ecfdf5; font-weight: bold; color: #047857;">${r.stats?.hadir || 0}</td>
        <td style="text-align: center; border: 1px solid #cbd5e1; background: #eff6ff; font-weight: bold; color: #1d4ed8;">${r.stats?.izin || 0}</td>
        <td style="text-align: center; border: 1px solid #cbd5e1; background: #fffbeb; font-weight: bold; color: #b45309;">${r.stats?.sakit || 0}</td>
        <td style="text-align: center; border: 1px solid #cbd5e1; background: #f5f3ff; font-weight: bold; color: #6d28d9;">${r.stats?.dispen || 0}</td>
        <td style="text-align: center; border: 1px solid #cbd5e1; background: #fef2f2; font-weight: bold; color: #b91c1c;">${r.stats?.alpa || 0}</td>
        <td style="text-align: center; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">${r.stats?.persentase || 0}%</td>
      </tr>
    `
  }).join('')

  const excelHtml = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
      <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"/>
      <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 10pt; }
        .header-title { font-size: 15pt; font-weight: bold; color: #0f172a; }
        .header-sub { font-size: 10pt; color: #475569; }
      </style>
    </head>
    <body>
      <div class="header-title">REKAPITULASI PRESENSI MULTI-PERTEMUAN SISWA</div>
      <div class="header-sub">SMK PPLG • PROGRAM KEAHLIAN PENGEMBANGAN PERANGKAT LUNAK DAN GIM</div>
      <br/>
      <table>
        <tr><td><b>Mata Pelajaran</b></td><td>: ${mapelNama}</td></tr>
        <tr><td><b>Kelas / Rombel</b></td><td>: ${kelasNama}</td></tr>
        <tr><td><b>Guru Pengampu</b></td><td>: ${guruNama}</td></tr>
        <tr><td><b>Total Pertemuan</b></td><td>: ${pertemuanList.length} Sesi Terlaksana</td></tr>
        <tr><td><b>Tanggal Cetak</b></td><td>: ${new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}</td></tr>
      </table>
      <br/>
      <table style="border-collapse: collapse; width: 100%;">
        <thead>
          <tr style="background: #047857; color: #ffffff;">
            <th style="border: 1px solid #cbd5e1; padding: 8px;">No</th>
            <th style="border: 1px solid #cbd5e1; padding: 8px;">Nama Lengkap Siswa</th>
            <th style="border: 1px solid #cbd5e1; padding: 8px;">Akun / Email</th>
            ${meetingHeaders}
            <th style="border: 1px solid #cbd5e1; padding: 6px; background: #065f46;">H</th>
            <th style="border: 1px solid #cbd5e1; padding: 6px; background: #065f46;">I</th>
            <th style="border: 1px solid #cbd5e1; padding: 6px; background: #065f46;">S</th>
            <th style="border: 1px solid #cbd5e1; padding: 6px; background: #065f46;">D</th>
            <th style="border: 1px solid #cbd5e1; padding: 6px; background: #065f46;">A</th>
            <th style="border: 1px solid #cbd5e1; padding: 6px; background: #065f46;">% Sah</th>
          </tr>
        </thead>
        <tbody>
          ${rowsHtml}
        </tbody>
      </table>
      <br/><br/>
      <table>
        <tr>
          <td colspan="4"></td>
          <td colspan="4" style="text-align: center;">
            Guru Pengampu Mata Pelajaran,<br/><br/><br/><br/>
            <b>${guruNama}</b>
          </td>
        </tr>
      </table>
    </body>
    </html>
  `

  const blob = new Blob([excelHtml], { type: 'application/vnd.ms-excel;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `Rekap_Presensi_Multi_Pertemuan_${kelasNama.replace(/\s+/g, '_')}.xls`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

