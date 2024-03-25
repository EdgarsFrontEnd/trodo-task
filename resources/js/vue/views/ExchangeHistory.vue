<template>
  <h1
    class="text-center mt-4 mb-8 text-xl lg:text-2xl xl:text-3xl font-semibold"
  >
    Add Latest Exchange Rate
  </h1>
  <div
    class="flex sm:gap-3.5 gap-2 sm:flex-row flex-col items-center sm:items-end justify-center"
  >
    <div>
      <label
        for="countries-from"
        class="block mb-2 text-sm font-medium text-black dark:text-white"
      >
        From
      </label>
      <select
        id="countries-from"
        v-model="selectedCurrencyFrom"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
      >
        <option
          v-for="(currency, code) in currencies"
          :key="code"
          :value="code"
        >
          {{ currency.name }}
        </option>
      </select>
    </div>

    <div>
      <label
        for="countries-to"
        class="block mb-2 text-sm font-medium text-black dark:text-white"
      >
        To
      </label>
      <select
        id="countries-to"
        v-model="selectedCurrencyTo"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
      >
        <option
          v-for="(currency, code) in currencies"
          :key="code"
          :value="code"
        >
          {{ currency.name }}
        </option>
      </select>
    </div>
    <button
      @click="
        handleFetchExchangeRates(selectedCurrencyFrom, selectedCurrencyTo)
      "
      class="inline-flex items-center bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
    >
      Add rate&nbsp;
      <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
        <path
          d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
          clip-rule="evenodd"
          fill-rule="evenodd"
        ></path>
      </svg>
    </button>
  </div>

  <div class="p-12 relative overflow-x-auto sm:rounded-lg">
    <table
      class="shadow-md w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
    >
      <thead
        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
      >
        <tr>
          <th scope="col" class="px-6 py-3">From</th>
          <th scope="col" class="px-6 py-3">To</th>
          <th scope="col" class="px-6 py-3">Exchange Rate</th>
          <th scope="col" class="px-6 py-3">Date & Time</th>
          <th scope="col" class="px-6 py-3">Delete</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="exchangeRate in exchangeRates"
          :key="exchangeRate.id"
          class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
        >
          <th
            scope="row"
            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
          >
            {{ exchangeRate.base_currency }}
          </th>
          <td
            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
          >
            {{ exchangeRate.target_currency }}
          </td>
          <td class="px-6 py-4">{{ exchangeRate.exchange_rate }}</td>
          <td class="px-6 py-4">{{ formatDate(exchangeRate.created_at) }}</td>
          <td class="px-6 py-4">
            <button
              @click="
                deleteExchangeRate(exchangeRate.id),
                  removeExchangeRateFromTable(exchangeRate.id)
              "
              class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
            >
              Delete
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <nav
      class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-4"
      aria-label="Table navigation"
    >
      <span
        class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto"
        >Showing
        <span class="font-semibold text-gray-900 dark:text-white">{{
          limit
        }}</span>
        of
        <span class="font-semibold text-gray-900 dark:text-white">{{
          totalRecords
        }}</span></span
      >
      <div class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
        <button
          @click="prevPage()"
          class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
        >
          Previous
        </button>
        <ul
          v-for="(page, index) in totalPages"
          :key="page"
          class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8"
        >
          <li>
            <a
              :class="[
                'cursor-pointer flex items-center justify-center px-3 h-8 border border-gray-300 dark:border-gray-700 dark:text-white',
                currentPage === index + 1
                  ? 'text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:bg-gray-700 dark:hover:bg-gray-700 dark:hover:text-white'
                  : 'text-gray-500 bg-white hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-400 dark:hover:text-white'
              ]"
              @click="goToPage(index + 1)"
              >{{ index + 1 }}</a
            >
          </li>
        </ul>
        <button
          @click="nextPage()"
          class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
        >
          Next
        </button>
      </div>
    </nav>
  </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { ref, computed, onMounted } from 'vue'
import {
  fetchExchangeRates,
  getExchangeRates,
  deleteExchangeRate,
  storeExchangeRate
} from '@/api/exchangeRates.js'
import currencies from '@/data/rates.json'

const router = useRouter()
const route = useRoute()

const selectedCurrencyFrom = ref('EUR')
const selectedCurrencyTo = ref('USD')

const exchangeRates = ref([])

const currentPage = ref(parseInt(route.query.page) || 1)
const limit = ref(parseInt(route.query.limit) || 8)
const totalRecords = ref(0)
const totalPages = computed(() => Math.ceil(totalRecords.value / limit.value))

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    handleGetExchangeRates()
    updateRouteQuery()
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    handleGetExchangeRates()
    updateRouteQuery()
  }
}

const goToPage = (pageNumber) => {
  currentPage.value = pageNumber
  handleGetExchangeRates()
  updateRouteQuery(pageNumber)
}

const updateRouteQuery = (page = null) => {
  router.push({
    query: { page: page ?? currentPage.value, limit: limit.value }
  })
}

onMounted(async () => {
  handleGetExchangeRates()
})

const formatDate = (dateTime) => {
  const date = new Date(dateTime)
  return date.toLocaleString()
}

const removeExchangeRateFromTable = (id) => {
  exchangeRates.value = exchangeRates.value.filter((rate) => rate.id !== id)
}

const handleGetExchangeRates = async () => {
  const response = await getExchangeRates({
    page: currentPage.value,
    limit: limit.value
  })
  exchangeRates.value = response.data
  totalRecords.value = response.total
}

const handleFetchExchangeRates = async (base, to) => {
  const results = await fetchExchangeRates(base)

  const data = {
    exchangeRates: {
      base_currency: base,
      target_currency: to,
      exchange_rate: results.rates[to]
    }
  }

  await storeExchangeRate(data)
  await handleGetExchangeRates()
}
</script>
