<template>
  <div class="card chart-container">
    <div class="chart-header">
      <p class="section-header">{{ title }}</p>
    </div>
    <div class="time-range-buttons">
      <button
        v-for="range in timeRanges"
        :key="range.value"
        @click="selectedTimeRange = range.value"
        :class="['global-btn', { active: selectedTimeRange === range.value }]"
      >
        {{ range.label }}
      </button>
    </div>
    <div class="chart-body">
      <div v-if="loading" class="text-center">
        <div class="spinner-border text-light" role="status">
          <span class="visually-hidden">Loading chart...</span>
        </div>
      </div>
      <apexchart
        v-else
        type="line"
        height="250"
        :options="chartOptions"
        :series="series"
      />
    </div>
  </div>
</template>

<script>
import VueApexCharts from "vue3-apexcharts";

export default {
  name: "PointsChart",
  components: {
    apexchart: VueApexCharts,
  },
  props: {
    title: {
      type: String,
      required: true,
    },
    chartType: {
      type: String,
      required: true,
      validator: (value) => ["overall", "soldier", "demoman"].includes(value),
    },
    pointsData: {
      type: Array,
      default: () => [],
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      selectedTimeRange: "week",
      timeRanges: [
        { label: "1W", value: "week" },
        { label: "1M", value: "month" },
        { label: "3M", value: "3months" },
        { label: "6M", value: "6months" },
        { label: "1Y", value: "year" },
        { label: "All", value: "all" },
      ],
    };
  },
  mounted() {
    this.setDefaultTimeRange();
  },
  watch: {
    pointsData() {
      this.setDefaultTimeRange();
    },
  },
  methods: {
    setDefaultTimeRange() {
      if (!this.pointsData.length) return;

      const timeRangeValues = [
        "week",
        "month",
        "3months",
        "6months",
        "year",
        "all",
      ];
      const pointsKey = `${this.chartType}_points`;
      const rankKey = `${this.chartType}_rank`;

      for (const range of timeRangeValues) {
        const filtered = this.getFilteredData(range);

        // Filter out duplicate points for this specific chart type
        const uniqueFiltered = filtered.filter((point, index) => {
          if (index === 0) return true;
          const prev = filtered[index - 1];
          return (
            point[pointsKey] !== prev[pointsKey] ||
            point[rankKey] !== prev[rankKey]
          );
        });

        if (uniqueFiltered.length >= 3) {
          this.selectedTimeRange = range;
          return;
        }
      }

      // If no range has 3+ points, default to "all"
      this.selectedTimeRange = "all";
    },
    getFilteredData(timeRange) {
      if (!this.pointsData.length || timeRange === "all") {
        return this.pointsData;
      }

      const now = Date.now() / 1000;
      const timeRanges = {
        week: 7 * 24 * 60 * 60,
        month: 30 * 24 * 60 * 60,
        "3months": 90 * 24 * 60 * 60,
        "6months": 180 * 24 * 60 * 60,
        year: 365 * 24 * 60 * 60,
      };

      const cutoffTime = now - timeRanges[timeRange];
      return this.pointsData.filter((point) => point.date >= cutoffTime);
    },
  },
  computed: {
    filteredPointsData() {
      return this.getFilteredData(this.selectedTimeRange);
    },
    chartOptions() {
      const colors = {
        overall: "#FF6B6B",
        soldier: "#4ECDC4",
        demoman: "#45B7D1",
      };

      const dropShadowColors = {
        overall: "red",
        soldier: "cyan",
        demoman: "blue",
      };

      const color = colors[this.chartType];
      const dropShadowColor = dropShadowColors[this.chartType];

      return {
        chart: {
          type: "line",
          height: 250,
          background: "rgba(255, 255, 255, 0.05)",
          dropShadow: {
            enabled: true,
            color: dropShadowColor,
            top: 5,
            left: 3,
            blur: 8,
            opacity: 0.5,
          },
          toolbar: {
            show: false,
          },
          zoom: {
            enabled: false,
          },
        },
        markers: {
          size: 1,
        },
        theme: {
          mode: "dark",
        },
        colors: [color],
        stroke: {
          curve: "straight",
          width: 3,
        },
        grid: {
          borderColor: "#444",
          strokeDashArray: 5,
        },
        xaxis: {
          type: "datetime",
          tooltip: {
            enabled: false,
          },
          labels: {
            tooltip: false,
            datetimeUTC: true,
            style: {
              colors: "#aaa",
            },
            formatter: function (value, timestamp) {
              const date = new Date(timestamp);
              const day = date.getUTCDate();
              const month = date.toLocaleDateString("en-US", {
                month: "short",
                timeZone: "UTC",
              });
              return `${day} ${month}`;
            },
            maxHeight: undefined,
            rotate: 0,
          },
          tickAmount: 6,
        },
        yaxis: {
          labels: {
            style: {
              colors: "#aaa",
            },
          },
        },
        tooltip: {
          theme: "dark",
          x: {
            format: "dd MMM yyyy",
          },
          custom: ({ series, seriesIndex, dataPointIndex, w }) => {
            const data =
              w.globals.initialSeries[seriesIndex].data[dataPointIndex];
            const date = new Date(data.x).toLocaleDateString("en-GB", {
              day: "2-digit",
              month: "short",
              year: "numeric",
              timeZone: "UTC",
            });
            const points = data.y;
            const rank = data[`${this.chartType}_rank`];
            const label =
              this.chartType.charAt(0).toUpperCase() + this.chartType.slice(1);

            return `
              <div class="apexcharts-tooltip-title" style="font-size: 12px; font-weight:bold; margin: 2px 0 5px; padding: 4px;">${date}</div>
              <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex; align-items: center; padding: 0 10px 0 10px;">
                <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: ${color};"></span>
                <div class="apexcharts-tooltip-text" style="font-size: 12px;">
                  <div class="apexcharts-tooltip-y-group">
                    <span class="apexcharts-tooltip-text-y-label">${label} points: </span>
                    <span class="apexcharts-tooltip-text-y-value">${points}</span>
                  </div>
                </div>
              </div>
              <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 2; display: flex; align-items: center; padding: 0 10px 7px 10px;">
                <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: ${color};"></span>
                <div class="apexcharts-tooltip-text" style="font-size: 12px;">
                  <div class="apexcharts-tooltip-y-group">
                    <span class="apexcharts-tooltip-text-y-label">${label} rank: </span>
                    <span class="apexcharts-tooltip-text-y-value">#${rank}</span>
                  </div>
                </div>
              </div>
            `;
          },
        },
        legend: {
          labels: {
            colors: "#fff",
          },
        },
      };
    },
    series() {
      if (!this.filteredPointsData.length) return [];

      const sortedData = [...this.filteredPointsData].sort(
        (a, b) => a.date - b.date
      );

      const pointsKey = `${this.chartType}_points`;
      const rankKey = `${this.chartType}_rank`;

      const filteredData = sortedData.filter((point, index) => {
        if (index === 0) return true;
        const prev = sortedData[index - 1];
        return (
          point[pointsKey] !== prev[pointsKey] ||
          point[rankKey] !== prev[rankKey]
        );
      });

      const label =
        this.chartType.charAt(0).toUpperCase() + this.chartType.slice(1);

      return [
        {
          name: `${label} Points`,
          data: filteredData.map((point) => ({
            x: point.date * 1000,
            y: point[pointsKey],
            [`${this.chartType}_rank`]: point[rankKey],
          })),
        },
      ];
    },
  },
};
</script>

<style scoped>
.vue-apexcharts {
  min-height: 200px !important;
  box-shadow: 0 0px 20px rgba(0, 0, 0, 0.75);
}

.chart-container {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  padding: 0 5px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  margin-bottom: 20px;
  color: #fff;
}

.section-header {
  color: var(--color-text);
  font-size: 20px;
  font-weight: bold;
  text-align: center;
  margin-bottom: 0;
}

.chart-body {
  padding: 0px 10px !important;
  border-radius: 0 0 10px 10px !important;
  margin-bottom: 20px;
}

.chart-header {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0 15px;
}

.time-range-buttons {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  padding: 0 15px 15px 15px;
}

.global-btn {
  padding: 0.5rem 1rem;
  border: 2px solid var(--color-border-soft);
  background: rgba(255, 255, 255, 0.1);
  color: var(--color-text);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-weight: 500;
  font-size: 0.9rem;
}

.global-btn:hover {
  background: rgba(74, 111, 165, 0.3);
  border-color: var(--color-primary);
}

.global-btn.active {
  background: rgba(74, 111, 165, 0.5);
  border-color: var(--color-primary);
  color: white;
}
</style>
