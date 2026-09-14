import React from 'react'

export default function Card({
  title,
  subtitle,
  action,
  badge,
  hover = false,
  children,
  footer,
  className = '',
  style = {},
  onClick,
  ...props
}) {
  const hasHeader = title || subtitle || action || badge

  return (
    <div
      className={`card ${hover ? 'card-hover' : ''} ${className}`}
      style={{ cursor: onClick ? 'pointer' : 'default', ...style }}
      onClick={onClick}
      {...props}
    >
      {hasHeader && (
        <div className="card-header">
          <div>
            <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
              {title && <h3 className="card-title">{title}</h3>}
              {badge}
            </div>
            {subtitle && <p style={{ fontSize: '0.825rem', color: 'var(--muted)', marginTop: '0.2rem' }}>{subtitle}</p>}
          </div>
          {action && <div>{action}</div>}
        </div>
      )}
      <div className="card-body">{children}</div>
      {footer && (
        <div
          style={{
            marginTop: '1.25rem',
            paddingTop: '0.75rem',
            borderTop: '1px solid var(--border)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'space-between',
            fontSize: '0.85rem'
          }}
        >
          {footer}
        </div>
      )}
    </div>
  )
}
