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
  is_paid: boolean
  price: number | null
  status: string
  image_url: string | null
  age_category: string | null
  owner: { name: string, role: string }
  participants: unknown[]
}

export interface EventData {
  bannerSrc: string
  eventDate: string
  eventTime: string
  title: string
  venueName: string
  venueAddress: string
  isPaid: boolean
  description: string
  organizer: {
    name: string
    role: string
  }
  participants: number
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
