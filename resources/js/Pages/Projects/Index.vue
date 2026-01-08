<script setup>
import { Head } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import LanguageChart from "@/Components/LanguageChart.vue";

const props = defineProps({
    projects: Array,
});

const searchQuery = ref("");
const selectedLanguage = ref("");
const viewMode = ref("grid");

const totalStars = computed(() => {
    return props.projects.reduce((sum, p) => sum + (p.stars || 0), 0);
});

const totalForks = computed(() => {
    return props.projects.reduce((sum, p) => sum + (p.forks || 0), 0);
});

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
    <Head title="DevVault - GitHub Portfolio" />

    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50"
    >
        <!-- Hero Header -->
        <div
            class="bg-gradient-to-r from-slate-900 via-purple-900 to-slate-900 text-white"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center space-y-6">
                    <div
                        class="inline-flex items-center gap-3 px-4 py-2 bg-white/10 rounded-full backdrop-blur-sm border border-white/20"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                            />
                        </svg>
                        <span class="text-sm font-medium"
                            >GitHub Portfolio</span
                        >
                    </div>

                    <h1 class="text-6xl font-black tracking-tight">
                        Dev<span class="text-indigo-400">Vault</span>
                    </h1>

                    <p
                        class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed"
                    >
                        Showcasing my coding journey through
                        <span class="font-bold text-white">{{
                            projects.length
                        }}</span>
                        repositories
                    </p>

                    <!-- Quick Stats -->
                    <div class="flex flex-wrap justify-center gap-8 pt-8">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-indigo-400">
                                {{ projects.length }}
                            </div>
                            <div class="text-sm text-gray-400 mt-1">
                                Repositories
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-yellow-400">
                                {{ totalStars }}
                            </div>
                            <div class="text-sm text-gray-400 mt-1">
                                Total Stars
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-green-400">
                                {{ totalForks }}
                            </div>
                            <div class="text-sm text-gray-400 mt-1">
                                Total Forks
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-purple-400">
                                {{ availableLanguages.length }}
                            </div>
                            <div class="text-sm text-gray-400 mt-1">
                                Languages
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
            <!-- Language Chart Section -->
            <div
                v-if="projects.length > 0"
                class="bg-white rounded-3xl shadow-lg p-8 border border-gray-100"
            >
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                        Language Distribution
                    </h2>
                    <p class="text-gray-600">
                        Technologies used across all repositories
                    </p>
                </div>
                <div class="h-80">
                    <LanguageChart :projects="projects" />
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div
                class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 sticky top-4 z-30 backdrop-blur-xl bg-white/95"
            >
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1 relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"
                        >
                            <svg
                                class="h-5 w-5 text-gray-400"
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
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search repositories..."
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all outline-none text-gray-900 placeholder-gray-400"
                        />
                    </div>

                    <div class="flex gap-4">
                        <select
                            v-model="selectedLanguage"
                            class="px-6 py-3.5 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none bg-white text-gray-900 font-medium min-w-[180px]"
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

                        <!-- View Mode Toggle -->
                        <div class="flex bg-gray-100 rounded-xl p-1">
                            <button
                                @click="viewMode = 'grid'"
                                :class="[
                                    'px-4 py-2 rounded-lg transition-all',
                                    viewMode === 'grid'
                                        ? 'bg-white shadow-sm text-indigo-600'
                                        : 'text-gray-600',
                                ]"
                            >
                                <svg
                                    class="w-5 h-5"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                    />
                                </svg>
                            </button>
                            <button
                                @click="viewMode = 'list'"
                                :class="[
                                    'px-4 py-2 rounded-lg transition-all',
                                    viewMode === 'list'
                                        ? 'bg-white shadow-sm text-indigo-600'
                                        : 'text-gray-600',
                                ]"
                            >
                                <svg
                                    class="w-5 h-5"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    v-if="searchQuery || selectedLanguage"
                    class="mt-4 flex items-center gap-2"
                >
                    <span class="text-sm text-gray-600"
                        >Showing {{ filteredProjects.length }} of
                        {{ projects.length }} repositories</span
                    >
                    <button
                        @click="
                            searchQuery = '';
                            selectedLanguage = '';
                        "
                        class="ml-auto text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                        Clear filters
                    </button>
                </div>
            </div>

            <!-- Projects Grid/List -->
            <div v-if="filteredProjects.length > 0">
                <!-- Grid View -->
                <div
                    v-if="viewMode === 'grid'"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <a
                        v-for="repo in filteredProjects"
                        :key="repo.name"
                        :href="repo.url"
                        target="_blank"
                        class="group bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 border-2 border-gray-100 hover:border-indigo-300 hover:-translate-y-1 flex flex-col overflow-hidden"
                    >
                        <div class="p-6 flex-1">
                            <div class="flex items-start gap-3 mb-4">
                                <div class="flex-shrink-0 pt-0.5">
                                    <svg
                                        class="w-5 h-5 text-gray-400 group-hover:text-indigo-500 transition-colors"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3
                                        class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-1 break-words"
                                        :title="repo.name"
                                    >
                                        {{ repo.name }}
                                    </h3>
                                    <span
                                        v-if="repo.language"
                                        class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700 border border-indigo-200 max-w-full truncate"
                                        :title="repo.language"
                                    >
                                        {{ repo.language }}
                                    </span>
                                </div>
                            </div>

                            <p
                                class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-2 h-10"
                            >
                                {{
                                    repo.description ||
                                    "No description available"
                                }}
                            </p>

                            <div
                                v-if="repo.topics && repo.topics.length > 0"
                                class="flex flex-wrap gap-2 mb-4"
                            >
                                <span
                                    v-for="topic in repo.topics.slice(0, 4)"
                                    :key="topic"
                                    class="text-xs text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg font-medium"
                                >
                                    #{{ topic }}
                                </span>
                            </div>
                        </div>

                        <div
                            class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200 flex items-center justify-between"
                        >
                            <div
                                class="flex items-center gap-4 text-sm text-gray-600"
                            >
                                <span
                                    class="flex items-center gap-1.5 font-medium"
                                >
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
                                <span
                                    class="flex items-center gap-1.5 font-medium"
                                >
                                    <svg
                                        class="w-4 h-4 text-blue-500"
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

                            <span
                                class="text-sm font-semibold text-indigo-600 group-hover:text-indigo-800 flex items-center gap-1"
                            >
                                View
                                <svg
                                    class="w-4 h-4 group-hover:translate-x-1 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6"
                                    />
                                </svg>
                            </span>
                        </div>
                    </a>
                </div>

                <!-- List View -->
                <div v-else class="space-y-4">
                    <a
                        v-for="repo in filteredProjects"
                        :key="repo.name"
                        :href="repo.url"
                        target="_blank"
                        class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border-2 border-gray-100 hover:border-indigo-300 p-6 flex items-center gap-6"
                    >
                        <div class="flex-shrink-0">
                            <div
                                class="w-16 h-16 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center"
                            >
                                <svg
                                    class="w-8 h-8 text-white"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <h3
                                    class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition-colors"
                                >
                                    {{ repo.name }}
                                </h3>
                                <span
                                    v-if="repo.language"
                                    class="px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700 border border-indigo-200"
                                >
                                    {{ repo.language }}
                                </span>
                            </div>
                            <p class="text-gray-600 text-sm mb-3 line-clamp-1">
                                {{
                                    repo.description ||
                                    "No description available"
                                }}
                            </p>
                            <div
                                class="flex items-center gap-6 text-sm text-gray-600"
                            >
                                <span
                                    class="flex items-center gap-1.5 font-medium"
                                >
                                    <svg
                                        class="w-4 h-4 text-yellow-500"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                        />
                                    </svg>
                                    {{ repo.stars }} stars
                                </span>
                                <span
                                    class="flex items-center gap-1.5 font-medium"
                                >
                                    <svg
                                        class="w-4 h-4 text-blue-500"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    {{ repo.forks }} forks
                                </span>
                                <div
                                    v-if="repo.topics && repo.topics.length > 0"
                                    class="flex gap-2 ml-auto"
                                >
                                    <span
                                        v-for="topic in repo.topics.slice(0, 3)"
                                        :key="topic"
                                        class="text-xs text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg font-medium"
                                    >
                                        #{{ topic }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex-shrink-0">
                            <span
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-50 text-indigo-600 font-semibold group-hover:bg-indigo-600 group-hover:text-white transition-colors"
                            >
                                View Repo
                                <svg
                                    class="w-4 h-4 group-hover:translate-x-1 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6"
                                    />
                                </svg>
                            </span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 mb-6"
                >
                    <svg
                        class="w-10 h-10 text-indigo-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                    No repositories found
                </h3>
                <p class="text-gray-600 mb-6">
                    Try adjusting your search or filter criteria
                </p>
                <button
                    @click="
                        searchQuery = '';
                        selectedLanguage = '';
                    "
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        />
                    </svg>
                    Reset Filters
                </button>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-slate-900 text-gray-400 mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center">
                    <p class="text-sm">
                        Built with <span class="text-red-500">❤️</span> using
                        Laravel 12, Vue 3 & Inertia.js
                    </p>
                    <p class="text-xs mt-2">
                        Data synced from GitHub API • Last updated:
                        {{ new Date().getFullYear() }}
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
