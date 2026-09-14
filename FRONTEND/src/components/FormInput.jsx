import React from 'react'

export default function FormInput({
  label,
  name,
  type = 'text',
  value,
  onChange,
  options = [],
  placeholder = '',
  required = false,
  disabled = false,
  helperText = '',
  error = '',
  rows = 3,
  min,
  max,
  accept,
  className = ''
}) {
  const inputProps = {
    id: name,
    name,
    required,
    disabled,
    placeholder,
    onChange
  }

  return (
    <div className={`form-group ${className}`}>
      {label && (
        <label htmlFor={name} className="form-label">
          <span>
            {label} {required && <span style={{ color: 'var(--danger)' }}>*</span>}
          </span>
        </label>
      )}

      {type === 'select' ? (
        <select
          {...inputProps}
          value={value ?? ''}
          className="form-select"
        >
          {placeholder && <option value="" disabled>{placeholder}</option>}
          {options.map((opt, i) => (
            <option key={opt.value ?? i} value={opt.value}>
              {opt.label}
            </option>
          ))}
        </select>
      ) : type === 'textarea' ? (
        <textarea
          {...inputProps}
          value={value ?? ''}
          rows={rows}
          className="form-textarea"
        />
      ) : type === 'file' ? (
        <input
          {...inputProps}
          type="file"
          accept={accept}
          className="form-input"
          style={{ padding: '0.45rem' }}
        />
      ) : (
        <input
          {...inputProps}
          type={type}
          value={value ?? ''}
          min={min}
          max={max}
          className="form-input"
        />
      )}

      {helperText && !error && <span className="form-helper">{helperText}</span>}
      {error && <span className="form-error">{error}</span>}
    </div>
  )
}
