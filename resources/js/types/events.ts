export interface RawEvent {
  id: number
  title: string
  description: string | null
  start: string | null
  end: string | null
  location: string | null
  address: string | null
  latitude: number | null
  longitude: number | null
  image_url: string | null
  is_paid: boolean
  price: number
  status: string
  age_category: string | null
  participants: unknown[]

  owner_type: string
  owner_id: number
  owner: {
    id: number | null
    first_name?: string | null
    last_name?: string | null
    name?: string
    avatar_url?: string
    group_url?: string
    users?: any[]
  } | null

  created_at: string
  updated_at: string
}

export interface EventMarker {
  id: number
  title: string
  start: string
  latitude: number
  longitude: number
  image_url: string | null
  location: string | null
  is_paid: boolean
  age_category: string | null
}
