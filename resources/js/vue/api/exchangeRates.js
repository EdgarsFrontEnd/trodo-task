import axios from 'axios'

const baseUrl = 'http://127.0.0.1:8000/'

export const storeExchangeRate = async (data) => {
  try {
    const response = await axios.post(`${baseUrl}api/exchange_rate/store`, data)
    return response.data
  } catch (error) {
    console.error(error)
    throw error
  }
}

export const getExchangeRate = async (base, to) => {
  try {
    const response = await axios.get(`${baseUrl}api/historical/${base}/${to}`)
    console.log(response.data)
    return response.data
  } catch (error) {
    console.error(error)
    throw error
  }
}

export const getExchangeRates = async (params = {}) => {
  try {
    const response = await axios.get(`${baseUrl}api/exchange_rates`, {
      params: params
    })
    return response.data
  } catch (error) {
    console.error(error)
    throw error
  }
}

export const updateExchangeRate = async (id, data) => {
  try {
    const response = await axios.put(`${baseUrl}api/exchange_rate/${id}`, data)
    return response.data
  } catch (error) {
    console.error(error)
    throw error
  }
}

export const deleteExchangeRate = async (id) => {
  try {
    const response = await axios.delete(`${baseUrl}api/exchange_rate/${id}`)
    return response.data
  } catch (error) {
    console.error(error)
    throw error
  }
}

export const fetchExchangeRates = async (base = null) => {
  try {
    const response = await axios.get(`${baseUrl}api/exchange_rates/external`, {
      params: {
        base: base
      }
    })
    return response.data
  } catch (error) {
    console.error(error)
    throw error
  }
}

export const fetchConvertedCurrency = async (base, to, amount) => {
  try {
    const response = await axios.get(`${baseUrl}api/exchange/convert`, {
      params: {
        base: base,
        to: to,
        amount: amount
      }
    })

    console.log(response.data)
    return response.data
  } catch (error) {
    console.error(error)
    throw error
  }
}
