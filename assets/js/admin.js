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

function formatNumber(value) {
  return Number(value || 0).toLocaleString('vi-VN');
}

function formatChartValue(value, unit) {
  if (!unit) {
    return formatNumber(value);
  }

  if (unit === 'đ') {
    return formatNumber(value) + ' đ';
  }

  return formatNumber(value) + ' ' + unit;
}

function getChartPadding(unit) {
  return unit === 'đ' ? 72 : 56;
}

function getAxisMax(maxValue, unit) {
  if (unit === 'đơn' || unit === 'Sản Phẩm') {
    if (maxValue <= 5) {
      return 5;
    }

    if (maxValue <= 10) {
      return 10;
    }

    return Math.ceil(maxValue / 5) * 5;
  }

  return Math.max(maxValue, 1);
}

function getAxisTicks(maxValue, unit) {
  if (unit === 'đơn' || unit === 'Sản Phẩm') {
    return [0, Math.ceil(maxValue / 2), maxValue];
  }

  return [0, maxValue / 2, maxValue];
}

function drawValueLabel(context, text, x, y) {
  context.font = '11px Segoe UI, Arial';
  context.textAlign = 'center';
  context.textBaseline = 'middle';

  const width = context.measureText(text).width + 12;
  const labelX = Math.max(width / 2 + 4, x);
  const labelY = Math.max(12, y);

  context.fillStyle = 'rgba(255, 255, 255, 0.92)';
  context.fillRect(labelX - width / 2, labelY - 9, width, 18);
  context.strokeStyle = '#eeeeee';
  context.strokeRect(labelX - width / 2, labelY - 9, width, 18);
  context.fillStyle = '#555555';
  context.fillText(text, labelX, labelY + 0.5);
}

function drawAxes(context, width, height, padding, maxValue, unit) {
  context.strokeStyle = '#e8e8e8';
  context.lineWidth = 1;
  context.beginPath();
  context.moveTo(padding, padding);
  context.lineTo(padding, height - padding);
  context.lineTo(width - padding / 2, height - padding);
  context.stroke();

  context.font = '11px Segoe UI, Arial';
  context.fillStyle = '#777777';
  context.textAlign = 'right';

  getAxisTicks(maxValue, unit).forEach(function (value) {
    const ratio = value / maxValue;
    const displayValue = unit === 'đơn' || unit === 'Sản Phẩm' ? value : Math.round(value);
    const y = height - padding - (height - padding * 2) * ratio;

    context.strokeStyle = '#f0f0f0';
    context.beginPath();
    context.moveTo(padding, y);
    context.lineTo(width - padding / 2, y);
    context.stroke();
    context.fillText(formatChartValue(displayValue, unit), padding - 8, y + 4);
  });
}

function drawBarChart(canvas, labels, values, color, unit) {
  const { context, width, height } = prepareCanvas(canvas);
  const padding = getChartPadding(unit);
  const maxValue = getAxisMax(Math.max(...values, 1), unit);
  const gap = 8;
  const barWidth = Math.max((width - padding * 2 - gap * (values.length - 1)) / values.length, 8);

  drawAxes(context, width, height, padding, maxValue, unit);
  context.font = '12px Segoe UI, Arial';
  context.textAlign = 'center';

  values.forEach(function (value, index) {
    const x = padding + index * (barWidth + gap);
    const barHeight = ((height - padding * 2) * value) / maxValue;
    const y = height - padding - barHeight;

    context.fillStyle = color;
    context.fillRect(x, y, barWidth, barHeight);
    context.fillStyle = '#666666';
    if (value > 0) {
      drawValueLabel(context, formatChartValue(value, unit), x + barWidth / 2, y - 12);
    }
    context.font = '12px Segoe UI, Arial';
    context.textAlign = 'center';
    context.textBaseline = 'alphabetic';
    context.fillStyle = '#666666';
    context.fillText(labels[index], x + barWidth / 2, height - 12);
  });
}

function drawLineChart(canvas, labels, values, color, unit) {
  const { context, width, height } = prepareCanvas(canvas);
  const padding = getChartPadding(unit);
  const maxValue = getAxisMax(Math.max(...values, 1), unit);
  const step = (width - padding * 2) / Math.max(values.length - 1, 1);
  const points = values.map(function (value, index) {
    return {
      x: padding + index * step,
      y: height - padding - ((height - padding * 2) * value) / maxValue,
    };
  });

  drawAxes(context, width, height, padding, maxValue, unit);
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
  let lastLabelRight = -Infinity;

  points.forEach(function (point, index) {
    context.fillStyle = '#ffffff';
    context.strokeStyle = color;
    context.lineWidth = 2;
    context.beginPath();
    context.arc(point.x, point.y, 4, 0, Math.PI * 2);
    context.fill();
    context.stroke();
    if (values[index] > 0) {
      const text = formatChartValue(values[index], unit);
      const textWidth = context.measureText(text).width + 18;
      const labelLeft = point.x - textWidth / 2;

      if (labelLeft > lastLabelRight + 6) {
        drawValueLabel(context, text, point.x, point.y - 14);
        lastLabelRight = point.x + textWidth / 2;
      }
    }

    context.font = '12px Segoe UI, Arial';
    context.textAlign = 'center';
    context.textBaseline = 'alphabetic';
    context.fillStyle = '#666666';
    context.fillText(labels[index], point.x, height - 12);
  });
}

function drawDoughnutChart(canvas, labels, values, unit) {
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
    context.fillText(label + ': ' + formatChartValue(values[index], unit), x + 16, y);
  });
}

function renderAdminCharts() {
  document.querySelectorAll('canvas[data-chart]').forEach(function (canvas) {
    const labels = getJsonData(canvas, 'labels');
    const values = getJsonData(canvas, 'values').map(Number);
    const color = canvas.dataset.color || '#ff7a3d';
    const unit = canvas.dataset.unit || '';

    if (canvas.dataset.chart === 'bar') {
      drawBarChart(canvas, labels, values, color, unit);
    }

    if (canvas.dataset.chart === 'line') {
      drawLineChart(canvas, labels, values, color, unit);
    }

    if (canvas.dataset.chart === 'doughnut') {
      drawDoughnutChart(canvas, labels, values, unit);
    }
  });
}

document.addEventListener('DOMContentLoaded', renderAdminCharts);
window.addEventListener('resize', renderAdminCharts);
