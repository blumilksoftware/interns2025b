import { reactive, ref } from 'vue'
import axios, { type AxiosError, type AxiosResponse } from 'axios'

export type ValidationErrors<T> = Partial<Record<keyof T, string>>

export interface ApiErrorResponse {
  message: string
  errors?: Record<string, string[]>
}

export interface ApiFormOptions<T, R = any> {
  endpoint: string
  method?: 'get' | 'post' | 'put' | 'patch' | 'delete'
  onSuccess?: (response: AxiosResponse<R>) => void
  onError?: (error: AxiosError) => void
  transform?: (data: T) => any
}

export function useApiForm<T extends Record<string, any>, R = any>(
  initialPayload: T,
  options: ApiFormOptions<T, R>,
) {
  const formData = reactive({ ...initialPayload }) as T
  const fieldErrors = reactive<ValidationErrors<T>>({})
  const globalMessage = ref<string>('')
  const isSubmitting = ref<boolean>(false)
  const isSuccess = ref<boolean>(false)

  function resetErrors() {
    const emptyErrors = {} satisfies ValidationErrors<T>
    Object.assign(fieldErrors, emptyErrors)
    globalMessage.value = ''
  }


  function reset() {
    Object.assign(formData, initialPayload)
    resetErrors()
    isSuccess.value = false
  }

  async function submitForm() {
    resetErrors()
    isSubmitting.value = true
    isSuccess.value = false

    try {
      const response = await axios.request<R>({
        url: options.endpoint,
        method: options.method ?? 'post',
        data: options.transform ? options.transform(formData) : formData,
      })

      isSuccess.value = true
      options.onSuccess?.(response)
      return response
    } catch (e) {
      const err = e as AxiosError<ApiErrorResponse>
      if (err.response?.status === 422 && err.response.data?.errors) {
        const errors = err.response.data.errors
        Object.entries(errors).forEach(([field, messages]) => {
          (fieldErrors as ValidationErrors<T>)[field as keyof T] = messages[0]
        })
      } else {
        globalMessage.value =
          err.response?.data?.message ?? 'An unexpected error occurred.'
        options.onError?.(err)
      }
      throw err
    } finally {
      isSubmitting.value = false
    }
  }

  return {
    formData,
    fieldErrors,
    globalMessage,
    isSubmitting,
    isSuccess,
    submitForm,
    reset,
    resetErrors,
  }
}
