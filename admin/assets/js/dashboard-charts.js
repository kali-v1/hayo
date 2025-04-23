/**
 * Dashboard Charts
 * 
 * This file contains the chart configurations for the admin dashboard.
 */

// Revenue Chart
function initRevenueChart(data) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const loadingElement = document.getElementById('revenueChartLoading');
    
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'الإيرادات الشهرية (ر.س)',
                data: data.values,
                backgroundColor: '#FF5733',
                borderColor: '#000000',
                borderWidth: 2,
                borderRadius: 4,
                hoverBackgroundColor: '#FF8C66',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                onComplete: function() {
                    // Hide loading indicator when animation is complete
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Tajawal',
                            size: 14
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#FFFFFF',
                    titleColor: '#000000',
                    bodyColor: '#000000',
                    borderColor: '#000000',
                    borderWidth: 2,
                    padding: 15,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toFixed(2) + ' ر.س';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#E0E0E0',
                        borderDash: [5, 5],
                    },
                    ticks: {
                        font: {
                            family: 'Tajawal',
                            size: 12
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Tajawal',
                            size: 12
                        }
                    }
                }
            }
        }
    });
    
    return revenueChart;
}

// User Registration Chart
function initUserRegistrationChart(data) {
    const ctx = document.getElementById('userRegistrationChart').getContext('2d');
    const loadingElement = document.getElementById('userRegistrationChartLoading');
    
    const userChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'تسجيلات المستخدمين',
                data: data.values,
                backgroundColor: 'rgba(52, 152, 219, 0.2)',
                borderColor: '#3498DB',
                borderWidth: 3,
                tension: 0.3,
                pointBackgroundColor: '#FFFFFF',
                pointBorderColor: '#000000',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                onComplete: function() {
                    // Hide loading indicator when animation is complete
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Tajawal',
                            size: 14
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#FFFFFF',
                    titleColor: '#000000',
                    bodyColor: '#000000',
                    borderColor: '#000000',
                    borderWidth: 2,
                    padding: 15,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#E0E0E0',
                        borderDash: [5, 5],
                    },
                    ticks: {
                        font: {
                            family: 'Tajawal',
                            size: 12
                        },
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Tajawal',
                            size: 12
                        }
                    }
                }
            }
        }
    });
    
    return userChart;
}

// Exam Completion Rate Chart (Doughnut)
function initExamCompletionChart(data) {
    const ctx = document.getElementById('examCompletionChart').getContext('2d');
    const loadingElement = document.getElementById('examCompletionChartLoading');
    
    const examChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['ناجح', 'راسب'],
            datasets: [{
                data: [data.passed, data.failed],
                backgroundColor: ['#2ECC71', '#E74C3C'],
                borderColor: '#000000',
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                onComplete: function() {
                    // Hide loading indicator when animation is complete
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            family: 'Tajawal',
                            size: 14
                        },
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: '#FFFFFF',
                    titleColor: '#000000',
                    bodyColor: '#000000',
                    borderColor: '#000000',
                    borderWidth: 2,
                    padding: 15,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${context.label}: ${percentage}% (${value})`;
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });
    
    return examChart;
}

// Instructor Exam Performance Chart (Stacked Bar)
function initInstructorExamChart(data) {
    const ctx = document.getElementById('instructorExamChart').getContext('2d');
    const loadingElement = document.getElementById('instructorExamChartLoading');
    
    const instructorExamChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'ناجح',
                    data: data.datasets[0].data,
                    backgroundColor: '#2ECC71',
                    borderColor: '#000000',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'راسب',
                    data: data.datasets[1].data,
                    backgroundColor: '#E74C3C',
                    borderColor: '#000000',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                onComplete: function() {
                    // Hide loading indicator when animation is complete
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Tajawal',
                            size: 14
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#FFFFFF',
                    titleColor: '#000000',
                    bodyColor: '#000000',
                    borderColor: '#000000',
                    borderWidth: 2,
                    padding: 15,
                    displayColors: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stacked: true,
                    grid: {
                        color: '#E0E0E0',
                        borderDash: [5, 5],
                    },
                    ticks: {
                        font: {
                            family: 'Tajawal',
                            size: 12
                        }
                    }
                },
                x: {
                    stacked: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Tajawal',
                            size: 12
                        }
                    }
                }
            }
        }
    });
    
    return instructorExamChart;
}

// Instructor Enrollment Distribution Chart (Pie)
function initInstructorEnrollmentChart(data) {
    const ctx = document.getElementById('instructorEnrollmentChart').getContext('2d');
    const loadingElement = document.getElementById('instructorEnrollmentChartLoading');
    
    const instructorEnrollmentChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.datasets[0].data,
                backgroundColor: [
                    '#3498DB', // Blue
                    '#2ECC71', // Green
                    '#F1C40F', // Yellow
                    '#E74C3C', // Red
                    '#9B59B6', // Purple
                    '#1ABC9C'  // Teal
                ],
                borderColor: '#000000',
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                onComplete: function() {
                    // Hide loading indicator when animation is complete
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        font: {
                            family: 'Tajawal',
                            size: 14
                        },
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: '#FFFFFF',
                    titleColor: '#000000',
                    bodyColor: '#000000',
                    borderColor: '#000000',
                    borderWidth: 2,
                    padding: 15,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${context.label}: ${value} طالب (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    return instructorEnrollmentChart;
}