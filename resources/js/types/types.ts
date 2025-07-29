export interface LoginForm {
  email: string
  password: string
  remember: boolean
}

export interface LoginResponse {
  token: string
}

export interface RegisterForm {
  first_name: string
  last_name: string
  email: string
  password: string
  password_confirmation: string
}

export interface ForgotPasswordForm {
  email: string
}

export interface AuthProps {
  auth: {
    user: { id: number, name: string, email: string } | null
  }
}

export interface UserDetail {
  id: number
  first_name: string
  last_name: string
  email: string
  events_count: number
  followers_count: number
  following_count: number
  events: Array<{ id: number, title: string, date: string }>
}

export interface EventForm {
  title: string
  description?: string
  start: string
  end: string
  location: string
  address?: string
  latitude?: number | null
  longitude?: number | null
  is_paid: boolean
  price?: number | null
  status: 'draft' | 'published' | 'cancelled'
  image_url?: string
  age_category?: string
}

export interface SelectOption {
  label: string
  value: string
}


