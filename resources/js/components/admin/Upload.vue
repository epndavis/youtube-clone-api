<template>
    <div class="container flex justify-center py-5">
        <div class="w-full md:w-3/4 lg:w-4/7 bg-white p-4">
            <form @submit.prevent="submit">
                <div ref="dropzone" class="mb-3 h-32 w-full border-4 border-dashed flex items-center justify-center hover:bg-gray-100">
                    <label v-if="!video" class="bg-blue-500 text-white rounded-sm py-2 px-3 text-sm font-medium cursor-pointer" for="file">
                        Choose file
                    </label>

                    <span v-else>{{ video.name }}</span>

                    <input id="file" class="hidden" type="file" @change="handle">
                </div>

                <input v-model="title" type="text" class="w-full mb-3 p-2 bg-gray-300 rounded-sm" placeholder="Video title">

                <textarea v-model="description" class="w-full mb-3 p-2 bg-gray-300 rounded-sm" rows="10" />

                <button class="uppercase bg-blue-500 text-white rounded-sm py-2 px-3 text-sm font-medium w-full">
                    <span v-if="!submission.submitting">Upload</span>
                    <span v-else>Uploading ...</span>
                </button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            title: '',
            description: '',
            video: null,
            submission: {
                submitting: false
            },
            errors: []
        }
    },

    mounted () {
        const self = this;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((event) => {
            self.$refs.dropzone.addEventListener(event, self.preventDefaults, false)
        })

        this.$refs.dropzone.addEventListener('drop', this.handleDrop, false)
    },

    beforeDestroy () {
        const self = this;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((event) => {
            self.$refs.dropzone.removeEventListener(event, self.preventDefaults, false)
        })

        this.$refs.dropzone.removeEventListener('drop', this.handleDrop, false)
    },

    methods: {
        handleDrop (e) {
            this.video = e.dataTransfer.files[0]
        },

        handle (e) {
            this.video = e.target.files[0]
        },

        preventDefaults (e) {
            e.preventDefault()
            e.stopPropagation()
        },

        submit () {
            if (!this.submission.submitting) {
                this.submission.submitting = true
                const formData = new FormData()

                formData.append('title', this.title)
                formData.append('description', this.description)
                formData.append('video', this.video)

                axios.post('/api/videos', formData)
                    .then((response) => {
                        this.submission.submitting = false
                        this.errors = []
                    }).catch((error) => {
                        this.submission.submitting = false
                        this.errors = error.response.data.errors
                    })
            }
        }
    }
}
</script>
