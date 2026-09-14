// Helper jurusan metadata & visual tokens
export const getJurusanInfo = (kelasNama = '') => {
  const upper = String(kelasNama).toUpperCase()
  if (upper.includes('PPLG')) {
    return {
      code: 'PPLG',
      name: 'Pengembangan Perangkat Lunak & Gim',
      icon: '💻',
      color: '#4f46e5',
      bg: 'rgba(79, 70, 229, 0.1)',
      border: 'rgba(79, 70, 229, 0.25)',
      badgeClass: 'badge-pplg'
    }
  }
  if (upper.includes('TKJ')) {
    return {
      code: 'TKJ',
      name: 'Teknik Komputer & Jaringan',
      icon: '🌐',
      color: '#0d9488',
      bg: 'rgba(13, 148, 136, 0.1)',
      border: 'rgba(13, 148, 136, 0.25)',
      badgeClass: 'badge-tkj'
    }
  }
  if (upper.includes('DKV')) {
    return {
      code: 'DKV',
      name: 'Desain Komunikasi Visual',
      icon: '🎨',
      color: '#d946ef',
      bg: 'rgba(217, 70, 239, 0.1)',
      border: 'rgba(217, 70, 239, 0.25)',
      badgeClass: 'badge-dkv'
    }
  }
  if (upper.includes('MPLB')) {
    return {
      code: 'MPLB',
      name: 'Manajemen Perkantoran & Bisnis',
      icon: '📋',
      color: '#d97706',
      bg: 'rgba(217, 119, 6, 0.1)',
      border: 'rgba(217, 119, 6, 0.25)',
      badgeClass: 'badge-mplb'
    }
  }
  return {
    code: 'UMUM',
    name: 'Program Keahlian Kejuruan',
    icon: '🏫',
    color: '#64748b',
    bg: 'rgba(100, 116, 139, 0.1)',
    border: 'rgba(100, 116, 139, 0.25)',
    badgeClass: 'badge-soft'
  }
}
