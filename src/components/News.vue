<template>
  <div
    class="container news-page py-4"
    style="background: var(--color-background)"
  >
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'tf2rjWeekly' }"
                  href="#"
                  @click="activeTab = 'tf2rjWeekly'"
                  >Newest TF2RJ Weekly Videos</a
                >
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'tempusArchives' }"
                  href="#"
                  @click="activeTab = 'tempusArchives'"
                  >Newest Tempus Archives Videos</a
                >
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'jumpTfForum' }"
                  href="#"
                  @click="activeTab = 'jumpTfForum'"
                  >Newest Jump.TF Forum Posts</a
                >
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div v-if="activeTab === 'tf2rjWeekly'" class="tab-content">
              <div
                v-for="video in tf2rjWeeklyVideos"
                :key="video.id"
                class="video-item"
              >
                <h5>{{ video.title }}</h5>
                <p>{{ video.date }}</p>
              </div>
            </div>
            <div v-if="activeTab === 'tempusArchives'" class="tab-content">
              <div
                v-for="video in tempusArchivesVideos"
                :key="video.id"
                class="video-item"
              >
                <h5>{{ video.title }}</h5>
                <p>{{ video.date }}</p>
              </div>
            </div>
            <div v-if="activeTab === 'jumpTfForum'" class="tab-content">
              <div
                v-for="post in jumpTfForumPosts"
                :key="post.id"
                class="post-item"
              >
                <h5>{{ post.title }}</h5>
                <p>{{ post.date }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "NewsPage",
  data() {
    return {
      activeTab: "tf2rjWeekly",
      tf2rjWeeklyVideos: [],
      tempusArchivesVideos: [],
      jumpTfForumPosts: [],
    };
  },
  async mounted() {
    await this.fetchTf2rjWeeklyVideos();
    await this.fetchTempusArchivesVideos();
    await this.fetchJumpTfForumPosts();
  },
  methods: {
    async fetchTf2rjWeeklyVideos() {
      // Replace with actual API call
      this.tf2rjWeeklyVideos = [
        { id: 1, title: "TF2RJ Weekly Video 1", date: "2023-10-01" },
        { id: 2, title: "TF2RJ Weekly Video 2", date: "2023-10-02" },
      ];
    },
    async fetchTempusArchivesVideos() {
      // Replace with actual API call
      this.tempusArchivesVideos = [
        { id: 1, title: "Tempus Archives Video 1", date: "2023-10-03" },
        { id: 2, title: "Tempus Archives Video 2", date: "2023-10-04" },
      ];
    },
    async fetchJumpTfForumPosts() {
      // Replace with actual API call
      this.jumpTfForumPosts = [
        { id: 1, title: "Jump.TF Forum Post 1", date: "2023-10-05" },
        { id: 2, title: "Jump.TF Forum Post 2", date: "2023-10-06" },
      ];
    },
  },
};
</script>

<style scoped>
.news-page {
  font-family: "Segoe UI";
}
.card {
  background: var(--color-dark);
  border: 1px solid rgba(42, 42, 42, 0.99);
  border-radius: 12px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}
.card-header {
  background: linear-gradient(135deg, var(--color-primary), var(--color-box));
  border-bottom: none;
}
.nav-tabs .nav-link {
  color: var(--color-text);
  font-weight: bold;
  border: 1px solid var(--color-border-soft);
}
.nav-tabs .nav-link.active {
  background: var(--color-primary);
  color: var(--color-text);
  border-color: var(--color-border-soft);
}
.tab-content {
  padding: 15px;
}
.video-item,
.post-item {
  background: var(--color-box);
  border-radius: 8px;
  padding: 10px;
  margin-bottom: 10px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  color: var(--color-text);
}
</style>
