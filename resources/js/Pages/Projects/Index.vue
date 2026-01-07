<script setup>
import { Head } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import LanguageChart from "@/Components/LanguageChart.vue";

const props = defineProps({
    projects: Array,
});

const searchQuery = ref("");
const selectedLanguage = ref("");

const availableLanguages = computed(() => {
    const languages = props.projects.map((p) => p.language).filter((l) => l);
    return [...new Set(languages)].sort();
});

const filteredProjects = computed(() => {
    return props.projects.filter((project) => {
        const matchesSearch =
            project.name
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            (project.description &&
                project.description
                    .toLowerCase()
                    .includes(searchQuery.value.toLowerCase()));

        const matchesLang =
            selectedLanguage.value === "" ||
            project.language === selectedLanguage.value;

        return matchesSearch && matchesLang;
    });
});
</script>

<template>
    <Head title="DevVault Portfolio" />

    <div
        class="min-h-screen bg-gray-50 text-gray-800 font-sans py-10 px-4 sm:px-6 lg:px-8"
    >
        <div class="max-w-7xl mx-auto space-y-12">
            <div class="text-center space-y-4">
                <h1
                    class="text-5xl font-extrabold text-gray-900 tracking-tight"
                >
                    DevVault
                </h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    A curated collection of my coding journey. <br />
                    Explore
                    <span class="font-bold text-indigo-600"
                        >{{ projects.length }} repositories</span
                    >
                    fetched live from GitHub.
                </p>
            </div>

            <div
                v-if="projects.length > 0"
                class="grid grid-cols-1 lg:grid-cols-3 gap-8"
            >
                <div
                    class="lg:col-span-2 bg-gradient-to-br from-indigo-900 to-slate-900 rounded-2xl p-8 text-white shadow-xl relative overflow-hidden flex flex-col justify-center"
                >
                    <div
                        class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"
                    ></div>
                    <div
                        class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"
                    ></div>

                    <div class="relative z-10">
                        <h2 class="text-3xl font-bold mb-2">Code Statistics</h2>
                        <div class="flex items-end gap-3 mb-6">
                            <span
                                class="text-7xl font-black tracking-tighter"
                                >{{ projects.length }}</span
                            >
                            <span
                                class="text-xl text-indigo-200 mb-2 font-medium"
                                >Total Repositories</span
                            >
                        </div>
                        <p
                            class="text-indigo-100/80 text-lg max-w-md leading-relaxed"
                        >
                            Data is synchronized using Laravel 12 Cache & GitHub
                            API. visualized with Chart.js and Vue 3.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-1 h-80">
                    <LanguageChart :projects="projects" />
                </div>
            </div>

            <div
                class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row gap-4 sticky top-4 z-20 backdrop-blur-md bg-white/90 supports-[backdrop-filter]:bg-white/60"
            >
                <div class="flex-1 relative">
                    <svg
                        class="absolute left-3 top-3.5 h-5 w-5 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        ></path>
                    </svg>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search projects by name or description..."
                        class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow outline-none"
                    />
                </div>

                <div class="w-full md:w-64">
                    <select
                        v-model="selectedLanguage"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white"
                    >
                        <option value="">All Languages</option>
                        <option
                            v-for="lang in availableLanguages"
                            :key="lang"
                            :value="lang"
                        >
                            {{ lang }}
                        </option>
                    </select>
                </div>
            </div>

            <div
                v-if="filteredProjects.length > 0"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
            >
                <div
                    v-for="repo in filteredProjects"
                    :key="repo.name"
                    class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:-translate-y-1 flex flex-col"
                >
                    <div class="p-6 flex-1">
                        <div class="flex justify-between items-start mb-4">
                            <h3
                                class="text-xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors truncate pr-2"
                            >
                                {{ repo.name }}
                            </h3>
                            <span
                                v-if="repo.language"
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100"
                            >
                                {{ repo.language }}
                            </span>
                        </div>

                        <p
                            class="text-gray-500 text-sm leading-relaxed mb-4 line-clamp-3 h-16"
                        >
                            {{
                                repo.description ||
                                "No description provided for this repository."
                            }}
                        </p>

                        <div
                            v-if="repo.topics && repo.topics.length > 0"
                            class="flex flex-wrap gap-2 mb-4"
                        >
                            <span
                                v-for="topic in repo.topics.slice(0, 3)"
                                :key="topic"
                                class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded"
                            >
                                #{{ topic }}
                            </span>
                        </div>
                    </div>

                    <div
                        class="px-6 py-4 bg-gray-50 rounded-b-2xl border-t border-gray-100 flex items-center justify-between"
                    >
                        <div class="flex space-x-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg
                                    class="w-4 h-4 text-yellow-500"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    />
                                </svg>
                                {{ repo.stars }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg
                                    class="w-4 h-4 text-gray-400"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                {{ repo.forks }}
                            </span>
                        </div>

                        <a
                            :href="repo.url"
                            target="_blank"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors"
                        >
                            View Repo <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-20">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4"
                >
                    <svg
                        class="w-8 h-8 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        ></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">
                    No projects found
                </h3>
                <p class="text-gray-500 mt-1">
                    Try adjusting your search or filter to find what you're
                    looking for.
                </p>
                <button
                    @click="
                        searchQuery = '';
                        selectedLanguage = '';
                    "
                    class="mt-4 text-indigo-600 hover:text-indigo-800 font-medium"
                >
                    Clear filters
                </button>
            </div>
        </div>
    </div>
</template>
