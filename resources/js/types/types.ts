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
