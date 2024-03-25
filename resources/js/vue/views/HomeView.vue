<template>
  <section class="flex flex-col items-center mt-8">
    <div class="absolute h-72 w-full bg-blue-900 -z-10 top-0"></div>
    <h1
      class="text-center mb-4 text-4xl lg:text-5xl xl:text-6xl font-semibold leading-none tracking-tight text-gray-100 dark:text-white"
    >
      TRODO Currency Convertor
    </h1>
    <p
      class="text-center mb-6 text-sm lg:text-xl font-normal text-gray-100 sm:px-16 xl:px-48 dark:text-gray-400"
    >
      Check live foreign currency exchange rates
    </p>

    <div class="rounded-lg drop-shadow-2xl bg-white m-12 py-6 px-4">
      <div class="flex sm:gap-3.5 gap-2 sm:flex-row flex-col">
        <div>
          <label
            for="amount"
            class="block mb-2 text-sm font-medium text-black dark:text-white"
            >Amount
            <span class="text-blue-700">{{
              rates[selectedCurrencyFrom].symbol
            }}</span>
          </label>
          <input
            type="number"
            id="amount"
            v-model="amountValue"
            class="outline-0 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          />
          <p
            :class="{ invisible: isAmountValueValid }"
            class="text-wrap text-red-500 text-sm pt-2"
          >
            Please enter an amount greater than 0.
          </p>
        </div>
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
        <button
          type="button"
          @click="swapCurrencies()"
          class="relative left-1/2 transform -translate-x-1/2 rotate-90 sm:rotate-0 sm:static sm:left-auto sm:transform-none sm:mt-7 mt-2 p-2.5 h-10 w-10 text-sm focus:outline-none bg-white rounded-full border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700"
        >
          <img :src="swapSvgIcon" alt="Swap" />
        </button>

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
      </div>
      <div class="flex justify-end">
        <button
          @click="handleConvert()"
          :disabled="!isAmountValueValid || isRequestingConvert"
          :class="{
            'opacity-50 cursor-not-allowed': !isAmountValueValid,
            'opacity-50': isRequestingConvert
          }"
          class="w-28 mt-4 bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
        >
          {{ isRequestingConvert ? 'Loading...' : 'Convert' }}
        </button>
      </div>
      <ExpandTransition v-if="convertedRate">
        <div>
          <p class="font-semibold text-gray-600">
            {{ convertedRate.amount.toFixed(2) }}
            {{ getCurrencyNameByCode(convertedRate.base) }}s =
          </p>
          <p class="text-2xl font-semibold text-gray-800 py-1">
            {{ convertedRate.converted }}
            {{ getCurrencyNameByCode(convertedRate.to) }}s
          </p>
          <p class="text-gray-600 text-sm">
            1 {{ convertedRate.base }} = {{ 1 * convertedRate.rate }}
            {{ convertedRate.to }}
          </p>
          <p class="text-gray-600 text-sm">
            1 {{ convertedRate.to }} =
            {{ parseFloat(1 / convertedRate.rate).toFixed(4) }}
            {{ convertedRate.base }}
          </p>
        </div>
      </ExpandTransition>
    </div>
    <RouterLink
      :to="{ name: 'ExchangeHistory' }"
      class="text-sm text-blue-500 underline"
      >See exchange history
    </RouterLink>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'
import rates from '@/data/rates.json'
import swapSvgIcon from '@assets/icons/swap.svg'
import { fetchConvertedCurrency } from '@/api/exchangeRates.js'
import ExpandTransition from '@/components/ExpandTransition.vue'

const currencies = rates
const selectedCurrencyFrom = ref('EUR')
const selectedCurrencyTo = ref('USD')
const amountValue = ref((1).toFixed(2))
const convertedRate = ref(null)
const isRequestingConvert = ref(false)

const isAmountValueValid = computed(() => {
  return amountValue.value >= 0
})

const swapCurrencies = () => {
  let temp = selectedCurrencyFrom.value
  selectedCurrencyFrom.value = selectedCurrencyTo.value
  selectedCurrencyTo.value = temp
}

const handleConvert = async () => {
  isRequestingConvert.value = true
  amountValue.value = parseFloat(amountValue.value).toFixed(2)
  const result = await fetchConvertedCurrency(
    selectedCurrencyFrom.value,
    selectedCurrencyTo.value,
    amountValue.value
  )
  convertedRate.value = result
  isRequestingConvert.value = false
}

const getCurrencyNameByCode = (currencyCode) => {
  return rates[currencyCode]?.name ?? currencyCode
}
</script>

<style scoped>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
