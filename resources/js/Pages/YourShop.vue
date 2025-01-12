<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    }
});
</script>
  <template>
      <Head title="YourShop" />

      <AuthenticatedLayout>
          <template #header>
              <h2 class="font-semibold text-xl text-gray-800 leading-tight">Twój sklep</h2>
          </template>

          <div class="py-6">
              <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                  <div v-if="items.length > 0">
                      <div class="w-full grid lg:grid-cols-5 md:grid-cols-3 grid-cols-1 gap-2 p-5 justify-center">
                          <div v-for="item in items" :key="item.id" class="w-full text-center">
                              <ProductCard 
                                  :image="item.screenshot_path"
                                  :name="item.model"
                                  :description="item.description"
                              />
                          </div>
                      </div>
                  </div>
                  <div v-else class="text-center py-12">
                      <p class="text-gray-600 text-lg">
                          Tutaj będą wyświetlane Twoje przyszłe projekty.
                      </p>
                  </div>
              </div>
          </div>
      </AuthenticatedLayout>
  </template>
