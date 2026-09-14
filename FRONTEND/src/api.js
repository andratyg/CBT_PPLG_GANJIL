import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api'
})

// Request Interceptor
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
    if (typeof config.headers.set === 'function') {
      config.headers.set('Authorization', `Bearer ${token}`)
    }
  }
  config.headers.Accept = 'application/json'
  return config
})

// Response Interceptor for handling 401 Unauthorized
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      // Clear invalid credentials
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      delete api.defaults.headers.common['Authorization']

      // If user is inside teacher dashboard, redirect to login
      const currentPath = window.location.pathname
      if (currentPath !== '/login' && currentPath !== '/' && !currentPath.startsWith('/siswa')) {
        console.warn('Sesi kedaluwarsa atau token tidak valid. Mengarahkan ke login...')
        window.location.href = '/login?expired=1'
      }
    }
    return Promise.reject(error)
  }
)

export default api
