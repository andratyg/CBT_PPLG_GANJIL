import { useState, useEffect } from 'react'

export function getInitialTheme() {
  const saved = localStorage.getItem('cbt_theme')
  if (saved === 'dark' || saved === 'light') return saved
  if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    return 'dark'
  }
  return 'light'
}

export function applyTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme)
  localStorage.setItem('cbt_theme', theme)
  window.dispatchEvent(new CustomEvent('cbt-theme-changed', { detail: theme }))
}

export function useTheme() {
  const [theme, setTheme] = useState(getInitialTheme)

  useEffect(() => {
    applyTheme(theme)

    const handleThemeChange = (e) => {
      if (e.detail && e.detail !== theme) {
        setTheme(e.detail)
      }
    }

    window.addEventListener('cbt-theme-changed', handleThemeChange)
    return () => window.removeEventListener('cbt-theme-changed', handleThemeChange)
  }, [theme])

  const toggleTheme = () => {
    const next = theme === 'dark' ? 'light' : 'dark'
    setTheme(next)
    applyTheme(next)
  }

  return { theme, toggleTheme, isDark: theme === 'dark' }
}
