// resources/js/Pages/PlantIdentifier.vue
<template>
  <div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-4">Plant Identifier</h1>
    <div class="mb-4">
      <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="plant_image">Upload plant image</label>
      <input
        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
        id="plant_image"
        type="file"
        @change="handleImageUpload"
      >
    </div>
    <button
      class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
      @click="identifyPlant"
      :disabled="!imageFile"
    >
      Identify Plant
    </button>
    <div v-if="loading" class="mt-4">
      <p>Identifying plant...</p>
    </div>
    <div v-if="plantInfo" class="mt-4">
      <h2 class="text-2xl font-bold">{{ plantInfo.name }}</h2>
      <p class="mt-2 whitespace-pre-line">{{ plantInfo.information }}</p>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

export default {
  setup() {
    const imageFile = ref(null)
    const loading = ref(false)
    const plantInfo = ref(null)
    const form = useForm({
      image: null,
    })

    const handleImageUpload = (event) => {
      imageFile.value = event.target.files[0]
      form.image = imageFile.value
    }

    const identifyPlant = () => {
      if (!imageFile.value) return

      loading.value = true
      plantInfo.value = null

      form.post('/identify', {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (response) => {
          plantInfo.value = response.props.plantInfo
          loading.value = false
        },
        onError: () => {
          loading.value = false
        },
      })
    }

    return {
      imageFile,
      loading,
      plantInfo,
      handleImageUpload,
      identifyPlant,
    }
  },
}
</script>