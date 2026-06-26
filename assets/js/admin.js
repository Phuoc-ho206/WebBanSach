function getJsonData(canvas, key) {
  try {
    return JSON.parse(canvas.dataset[key] || '[]');
  } catch (error) {
    return [];
  }
}

function prepareCanvas(canvas) {
  const ratio = window.devicePixelRatio || 1;
  const width = canvas.clientWidth || 320;
  const height = canvas.clientHeight || 260;

  canvas.width = width * ratio;
  canvas.height = height * ratio;

  const context = canvas.getContext('2d');
  context.setTransform(ratio, 0, 0, ratio, 0, 0);
  context.clearRect(0, 0, width, height);

  return { context, width, height };
}

function drawAxes(context, width, height, padding) {
  context.strokeStyle = '#e8e8e8';
  context.lineWidth = 1;
  context.beginPath();
  context.moveTo(padding, padding);
  context.lineTo(padding, height - padding);
  context.lineTo(width - padding / 2, height - padding);
  context.stroke();
}

function drawBarChart(canvas, labels, values, color) {
  const { context, width, height } = prepareCanvas(canvas);
  const padding = 36;
  const maxValue = Math.max(...values, 1);
  const gap = 8;
  const barWidth = Math.max((width - padding * 2 - gap * (values.length - 1)) / values.length, 8);

  drawAxes(context, width, height, padding);
  context.font = '12px Segoe UI, Arial';
  context.textAlign = 'center';

  values.forEach(function (value, index) {
    const x = padding + index * (barWidth + gap);
    const barHeight = ((height - padding * 2) * value) / maxValue;
    const y = height - padding - barHeight;

    context.fillStyle = color;
    context.fillRect(x, y, barWidth, barHeight);
    context.fillStyle = '#666666';
    context.fillText(labels[index], x + barWidth / 2, height - 12);
  });
}

function drawLineChart(canvas, labels, values, color) {
  const { context, width, height } = prepareCanvas(canvas);
  const padding = 36;
  const maxValue = Math.max(...values, 1);
  const step = (width - padding * 2) / Math.max(values.length - 1, 1);
  const points = values.map(function (value, index) {
    return {
      x: padding + index * step,
      y: height - padding - ((height - padding * 2) * value) / maxValue,
    };
  });

  drawAxes(context, width, height, padding);
  context.strokeStyle = color;
  context.lineWidth = 3;
  context.beginPath();

  points.forEach(function (point, index) {
    if (index === 0) {
      context.moveTo(point.x, point.y);
      return;
    }

    context.lineTo(point.x, point.y);
  });

  context.stroke();
  context.font = '12px Segoe UI, Arial';
  context.textAlign = 'center';

  points.forEach(function (point, index) {
    context.fillStyle = '#ffffff';
    context.strokeStyle = color;
    context.lineWidth = 2;
    context.beginPath();
    context.arc(point.x, point.y, 4, 0, Math.PI * 2);
    context.fill();
    context.stroke();
    context.fillStyle = '#666666';
    context.fillText(labels[index], point.x, height - 12);
  });
}

function drawDoughnutChart(canvas, labels, values) {
  const { context, width, height } = prepareCanvas(canvas);
  const colors = ['#ff7a3d', '#4a9b7f', '#2196f3', '#ffa500', '#ff4444', '#6c5ce7'];
  const total = values.reduce(function (sum, value) {
    return sum + value;
  }, 0) || 1;
  const centerX = width / 2;
  const centerY = height / 2 - 10;
  const radius = Math.min(width, height) / 3.3;
  let startAngle = -Math.PI / 2;

  values.forEach(function (value, index) {
    const slice = (value / total) * Math.PI * 2;

    context.beginPath();
    context.moveTo(centerX, centerY);
    context.arc(centerX, centerY, radius, startAngle, startAngle + slice);
    context.closePath();
    context.fillStyle = colors[index % colors.length];
    context.fill();

    startAngle += slice;
  });

  context.beginPath();
  context.arc(centerX, centerY, radius * 0.58, 0, Math.PI * 2);
  context.fillStyle = '#ffffff';
  context.fill();

  context.font = '12px Segoe UI, Arial';
  context.textAlign = 'left';
  labels.slice(0, 4).forEach(function (label, index) {
    const x = 12 + (index % 2) * (width / 2);
    const y = height - 34 + Math.floor(index / 2) * 18;

    context.fillStyle = colors[index % colors.length];
    context.fillRect(x, y - 9, 10, 10);
    context.fillStyle = '#666666';
    context.fillText(label, x + 16, y);
  });
}

function renderAdminCharts() {
  document.querySelectorAll('canvas[data-chart]').forEach(function (canvas) {
    const labels = getJsonData(canvas, 'labels');
    const values = getJsonData(canvas, 'values').map(Number);
    const color = canvas.dataset.color || '#ff7a3d';

    if (canvas.dataset.chart === 'bar') {
      drawBarChart(canvas, labels, values, color);
    }

    if (canvas.dataset.chart === 'line') {
      drawLineChart(canvas, labels, values, color);
    }

    if (canvas.dataset.chart === 'doughnut') {
      drawDoughnutChart(canvas, labels, values);
    }
  });
}

document.addEventListener('DOMContentLoaded', renderAdminCharts);
window.addEventListener('resize', renderAdminCharts);
