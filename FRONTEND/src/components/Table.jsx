import React from 'react'

export default function Table({
  columns = [],
  data = [],
  emptyMessage = 'Tidak ada data ditemukan',
  loading = false,
  onRowClick,
  className = ''
}) {
  return (
    <div className={`table-responsive ${className}`}>
      <table className="table">
        <thead>
          <tr>
            {columns.map((col, idx) => (
              <th
                key={col.key || idx}
                style={{
                  width: col.width || 'auto',
                  textAlign: col.align || 'left'
                }}
              >
                {col.header}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {loading ? (
            <tr>
              <td colSpan={columns.length} style={{ textAlign: 'center', padding: '2.5rem' }}>
                <div style={{ display: 'inline-flex', alignItems: 'center', gap: '0.75rem', color: 'var(--muted)' }}>
                  <span
                    style={{
                      width: '1.2rem',
                      height: '1.2rem',
                      border: '2px solid var(--accent)',
                      borderRightColor: 'transparent',
                      borderRadius: '50%',
                      animation: 'spin 0.6s linear infinite'
                    }}
                  />
                  <span>Memuat data...</span>
                </div>
              </td>
            </tr>
          ) : data.length === 0 ? (
            <tr>
              <td colSpan={columns.length} style={{ textAlign: 'center', padding: '2.5rem' }}>
                <div className="empty-state" style={{ padding: '1rem' }}>
                  <div className="empty-state-icon">📂</div>
                  <div className="empty-state-title">{emptyMessage}</div>
                </div>
              </td>
            </tr>
          ) : (
            data.map((row, rIdx) => (
              <tr
                key={row.id || rIdx}
                onClick={() => onRowClick && onRowClick(row)}
                style={{ cursor: onRowClick ? 'pointer' : 'default' }}
              >
                {columns.map((col, cIdx) => (
                  <td
                    key={col.key || cIdx}
                    style={{ textAlign: col.align || 'left' }}
                  >
                    {col.render ? col.render(row, rIdx) : row[col.key]}
                  </td>
                ))}
              </tr>
            ))
          )}
        </tbody>
      </table>
    </div>
  )
}
