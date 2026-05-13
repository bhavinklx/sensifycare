@extends('admin.layouts.app')

@section('content')
<div class="app-hero-header d-flex align-items-center">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <i class="ri-home-8-line lh-1 pe-3 me-3 border-end"></i>
            <a href="{{ route('dashboard') }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item text-primary" aria-current="page">
            AI Assistant
        </li>
    </ol>
</div>

<div class="app-body">
    <div class="row gx-3">
        <div class="col-xxl-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="ri-robot-2-line me-2 fs-4"></i> Sensify Care AI Assistant
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small fw-bold mb-0 text-white-50"><i class="ri-translate-2"></i> Language:</label>
                        <select id="chatLanguage" class="form-select form-select-sm border-0 bg-white bg-opacity-10 text-white shadow-none" style="width: auto; cursor: pointer;">
                            <option value="en" class="text-dark">English</option>
                            <option value="hi" class="text-dark">Hindi</option>
                            <option value="gu" class="text-dark">Gujarati</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="d-flex flex-column" style="height: calc(100vh - 250px); min-height: 500px;">
                        <!-- Chat Messages Area -->
                        <div id="chatMessages" class="flex-grow-1 p-4 overflow-auto bg-light" style="background-color: #f8f9fa;">
                            <div class="message-container ai-message mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="avatar-box sm bg-primary text-white me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="ri-robot-2-fill fs-5"></i>
                                    </div>
                                    <div class="message-bubble p-3 rounded-3 bg-white shadow-sm border" style="max-width: 80%;">
                                        Hello! How can I help you today? You can ask me health questions or upload a medical report for analysis.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Input Area -->
                        <div class="p-3 border-top bg-white">
                            <form id="aiChatForm">
                                <div class="d-flex flex-column gap-2">
                                    <!-- File Status Bar -->
                                    <div id="fileStatus" class="small text-muted d-none mb-1">
                                        <div class="d-inline-flex align-items-center bg-light px-2 py-1 rounded border">
                                            <i class="ri-file-check-line text-success me-1"></i> 
                                            <span id="fileNameDisplay" class="text-truncate" style="max-width: 200px;"></span>
                                            <button type="button" id="removeFileBtn" class="btn btn-link btn-sm text-danger p-0 ms-2" style="text-decoration: none;"><i class="ri-close-circle-fill"></i></button>
                                        </div>
                                    </div>

                                    <!-- Input Bar: Attach | Textarea | Send -->
                                    <div class="d-flex align-items-end gap-3">
                                        <!-- Attach Icon -->
                                        <div class="mb-1">
                                            <label for="chatFile" class="btn btn-light border rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; cursor: pointer; transition: all 0.2s;" data-bs-toggle="tooltip" title="Attach Medical Report">
                                                <i class="ri-attachment-2 text-primary fs-4"></i>
                                                <input type="file" id="chatFile" class="d-none" accept=".jpg,.jpeg,.png,.pdf,.xlsx,.xls">
                                            </label>
                                        </div>

                                        <!-- Textarea -->
                                        <div class="flex-grow-1">
                                            <textarea id="chatQuestion" class="form-control shadow-none border-light-subtle" rows="1" placeholder="Type your health question or analyze a report..." style="resize: none; border-radius: 24px; transition: all 0.3s; padding: 12px 20px; min-height: 48px; font-size: 1rem;"></textarea>
                                        </div>

                                        <!-- Send Button -->
                                        <div class="mb-1">
                                            <button type="submit" id="sendBtn" class="btn btn-primary d-flex align-items-center justify-content-center shadow rounded-circle" style="width: 48px; height: 48px; transition: transform 0.2s;">
                                                <i class="ri-send-plane-2-fill fs-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .message-bubble {
        word-wrap: break-word;
        font-size: 1rem;
        line-height: 1.6;
        color: #333;
    }
    .user-message .d-flex {
        flex-direction: row-reverse;
    }
    .user-message .message-bubble {
        background-color: #007bff !important;
        color: white !important;
        border: none !important;
        margin-left: auto;
        margin-right: 1rem;
        border-radius: 20px 20px 0 20px !important;
    }
    .ai-message .message-bubble {
        background-color: #fff !important;
        border: 1px solid #eef0f2 !important;
        border-radius: 20px 20px 20px 0 !important;
    }
    #chatQuestion:focus {
        border-color: #007bff !important;
        background-color: #fff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1) !important;
    }
    #sendBtn:hover {
        transform: scale(1.05);
    }
    #sendBtn:active {
        transform: scale(0.95);
    }
    #chatMessages::-webkit-scrollbar {
        width: 6px;
    }
    #chatMessages::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.1);
        border-radius: 10px;
    }
    .typing-indicator span {
        height: 8px;
        width: 8px;
        float: left;
        margin: 0 2px;
        background-color: #007bff;
        display: block;
        border-radius: 50%;
        opacity: 0.4;
    }
    .typing-indicator span:nth-of-type(1) { animation: 1s blink infinite 0.3333s; }
    .typing-indicator span:nth-of-type(2) { animation: 1s blink infinite 0.6666s; }
    .typing-indicator span:nth-of-type(3) { animation: 1s blink infinite 0.9999s; }
    @keyframes blink { 50% { opacity: 1; } }

    /* Progressive Flow Styles */
    .progressive-step {
        animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .hook-button {
        background: #f0f7ff;
        color: #007bff;
        border: 1px dashed #007bff;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-top: 15px;
    }
    .hook-button:hover {
        background: #007bff;
        color: white;
        border-style: solid;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-yellow { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .status-green { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .status-red { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

@endsection

@section('page-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('aiChatForm');
    const chatMessages = document.getElementById('chatMessages');
    const chatQuestion = document.getElementById('chatQuestion');
    const chatLanguage = document.getElementById('chatLanguage');
    const fileInput = document.getElementById('chatFile');
    const fileStatus = document.getElementById('fileStatus');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const removeFileBtn = document.getElementById('removeFileBtn');
    const sendBtn = document.getElementById('sendBtn');

    // Auto-resize textarea
    chatQuestion.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        if (this.scrollHeight > 200) {
            this.style.overflowY = 'auto';
            this.style.height = '200px';
        } else {
            this.style.overflowY = 'hidden';
        }
    });

    // Handle Enter to Send
    chatQuestion.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.requestSubmit();
        }
    });

    // Handle File Selection UI
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            fileNameDisplay.textContent = this.files[0].name;
            fileStatus.classList.remove('d-none');
        } else {
            fileStatus.classList.add('d-none');
        }
    });

    // Handle File Removal
    removeFileBtn.addEventListener('click', function() {
        fileInput.value = '';
        fileStatus.classList.add('d-none');
    });

    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const language = chatLanguage.value;
        const question = chatQuestion.value.trim();
        const hasFile = fileInput.files.length > 0;

        if (!question && !hasFile) return;

        // Disable UI
        sendBtn.disabled = true;
        chatQuestion.disabled = true;

        // Append user message
        let userMsgText = question;
        if (hasFile) {
            userMsgText = (question ? `<div class="mb-2"><strong>Question:</strong> ${question}</div>` : '') + 
                        `<div class="d-flex align-items-center bg-white bg-opacity-10 p-2 rounded border border-white border-opacity-20 small">
                            <i class="ri-file-search-line me-2 fs-5"></i> Analyzing Report: <em>${fileInput.files[0].name}</em>
                        </div>`;
        }
        appendMessage('user', userMsgText);

        // Show typing indicator
        const indicatorId = showTypingIndicator();

        let finalFileUrl = '';
        const type = hasFile ? 'file' : 'text';

        try {
            if (hasFile) {
                const formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('_token', '{{ csrf_token() }}');

                const uploadRes = await fetch('{{ route("analyze-report-upload") }}', {
                    method: 'POST',
                    body: formData
                });
                const uploadData = await uploadRes.json();
                
                if (!uploadData.success) {
                    throw new Error(uploadData.message || 'File upload failed');
                }
                finalFileUrl = uploadData.file_url;
            }

            // Clear input
            chatQuestion.value = '';
            fileInput.value = '';
            fileStatus.classList.add('d-none');
            chatQuestion.style.height = 'auto';

            // Send to Analysis API
            const payload = {
                _token: '{{ csrf_token() }}',
                type: type,
                language: language,
                question: question,
                text: question,
                file_url: finalFileUrl
            };

            const response = await fetch('{{ route("analyze-report") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });
            const data = await response.json();

            removeTypingIndicator(indicatorId);
            if (data.success === false) {
                appendMessage('ai', 'Error: ' + (data.message || 'Something went wrong.'));
            } else {
                if (data.data && data.data.step1) {
                    renderProgressiveFlow(data.data);
                } else {
                    const aiResponse = data.answer || data.analysis || data.message || JSON.stringify(data);
                    appendMessage('ai', aiResponse);
                }
            }
        } catch (error) {
            removeTypingIndicator(indicatorId);
            appendMessage('ai', 'Error: ' + error.message);
            console.error('Error:', error);
        } finally {
            sendBtn.disabled = false;
            chatQuestion.disabled = false;
            setTimeout(() => chatQuestion.focus(), 100);
        }
    });

    function appendMessage(role, text) {
        const div = document.createElement('div');
        div.className = `message-container ${role}-message mb-3`;
        
        const icon = role === 'ai' ? 'ri-robot-2-fill' : 'ri-user-fill';
        const bg = role === 'ai' ? 'bg-primary' : 'bg-secondary';
        const avatarSize = role === 'ai' ? '40px' : '32px';

        div.innerHTML = `
            <div class="d-flex align-items-start">
                <div class="avatar-box sm ${bg} text-white ${role === 'ai' ? 'me-3' : 'ms-3'} rounded-circle d-flex align-items-center justify-content-center" style="width: ${avatarSize}; height: ${avatarSize}; flex-shrink: 0;">
                    <i class="${icon} ${role === 'ai' ? 'fs-5' : ''}"></i>
                </div>
                <div class="message-bubble p-3 rounded-3 shadow-sm border" style="max-width: 80%;">
                    ${typeof text === 'string' ? text.replace(/\n/g, '<br>') : text}
                </div>
            </div>
        `;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return div;
    }

    function renderProgressiveFlow(data) {
        const container = appendMessage('ai', '<div id="progressiveFlowContainer"></div>');
        const flowRoot = container.querySelector('#progressiveFlowContainer');
        
        function renderStep1() {
            const step1 = data.step1;
            const statusClass = `status-${step1.status.toLowerCase()}`;
            
            const html = `
                <div class="progressive-step" id="step1">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="fw-bold text-primary fs-6"><i class="ri-survey-line me-1"></i> Step 1: Summary</span>
                        <span class="status-badge ${statusClass}">${step1.status_label}</span>
                    </div>
                    <ul class="ps-3 mb-3">
                        ${step1.takeaways.map(t => `<li class="mb-2 text-dark">${t}</li>`).join('')}
                    </ul>
                    <div class="p-3 bg-light rounded-3 border-start border-success border-4 mb-3">
                        <div class="small fw-bold text-success mb-1"><i class="ri-shield-check-line"></i> Reassurance</div>
                        <div class="text-dark small">${step1.reassurance}</div>
                    </div>
                    ${step1.hook ? `<button class="hook-button" onclick="window.revealStep(2)">${step1.hook} <i class="ri-arrow-right-s-line"></i></button>` : ''}
                </div>
            `;
            flowRoot.innerHTML = html;
        }

        window.revealStep = function(stepNum) {
            const currentHook = flowRoot.querySelector('.hook-button');
            if (currentHook) {
                currentHook.style.opacity = '0';
                setTimeout(() => currentHook.remove(), 300);
            }

            if (stepNum === 2) {
                const step2 = data.step2;
                const html = `
                    <div class="progressive-step mt-4 pt-4 border-top" id="step2">
                        <div class="fw-bold text-primary mb-3 fs-6"><i class="ri-body-scan-line me-1"></i> Step 2: System Breakdown</div>
                        ${step2.modules.map(m => `
                            <div class="mb-4 p-3 bg-white border rounded-3 shadow-xs">
                                <div class="fw-bold text-dark border-bottom pb-2 mb-2 d-flex align-items-center">
                                    <i class="ri-focus-2-line me-2 text-primary"></i> ${m.category}
                                </div>
                                <div class="small text-muted mb-3 lh-base">${m.interpretation}</div>
                                <div class="small bg-light p-2 rounded border-start border-primary border-3">
                                    <strong>Context:</strong> ${m.comparison}
                                </div>
                            </div>
                        `).join('')}
                        ${step2.hook ? `<button class="hook-button" onclick="window.revealStep(3)">${step2.hook} <i class="ri-arrow-right-s-line"></i></button>` : ''}
                    </div>
                `;
                flowRoot.insertAdjacentHTML('beforeend', html);
            } else if (stepNum === 3) {
                const step3 = data.step3;
                const html = `
                    <div class="progressive-step mt-4 pt-4 border-top" id="step3">
                        <div class="fw-bold text-primary mb-3 fs-6"><i class="ri-leaf-line me-1"></i> Step 3: Advice & Roadmap</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="h-100 p-3 bg-info-subtle rounded-3 border border-info-subtle">
                                    <div class="fw-bold text-info-emphasis mb-2 d-flex align-items-center">
                                        <i class="ri-restaurant-line me-2"></i> Dietary Advice
                                    </div>
                                    <div class="small text-dark lh-base">${step3.lifestyle.diet}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="h-100 p-3 bg-success-subtle rounded-3 border border-success-subtle">
                                    <div class="fw-bold text-success-emphasis mb-2 d-flex align-items-center">
                                        <i class="ri-run-line me-2"></i> Activity Plan
                                    </div>
                                    <div class="small text-dark lh-base">${step3.lifestyle.activity}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 p-3 bg-warning-subtle rounded-3 border border-warning-subtle">
                            <div class="fw-bold text-warning-emphasis mb-2 d-flex align-items-center">
                                <i class="ri-map-pin-user-line me-2"></i> Medical Roadmap
                            </div>
                            <div class="text-dark mb-2">${step3.medical_roadmap.specialist}</div>
                            <div class="small text-muted bg-white bg-opacity-50 p-2 rounded">
                                <strong>Your Doctor Checklist:</strong> ${step3.medical_roadmap.checklist}
                            </div>
                        </div>
                        ${step3.hook ? `<button class="hook-button" onclick="window.revealStep(4)">${step3.hook} <i class="ri-arrow-right-s-line"></i></button>` : ''}
                    </div>
                `;
                flowRoot.insertAdjacentHTML('beforeend', html);
            } else if (stepNum === 4) {
                const step4 = data.step4;
                const html = `
                    <div class="progressive-step mt-4 pt-4 border-top" id="step4">
                        <div class="fw-bold text-primary mb-3 fs-6"><i class="ri-flask-line me-1"></i> Step 4: Next Steps</div>
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body p-3">
                                <div class="mb-3 d-flex align-items-start">
                                    <div class="bg-primary text-white p-2 rounded me-3">
                                        <i class="ri-microscope-line"></i>
                                    </div>
                                    <div>
                                        <div class="small fw-bold">Recommended Testing</div>
                                        <div class="small text-muted">${step4.reflex_testing}</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div class="bg-primary text-white p-2 rounded me-3">
                                        <i class="ri-calendar-event-line"></i>
                                    </div>
                                    <div>
                                        <div class="small fw-bold">Follow-up Timeline</div>
                                        <div class="small text-muted">${step4.follow_up_timeline}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-3 mb-4">
                            ${step4.cta_buttons.map(btn => `<button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">${btn}</button>`).join('')}
                        </div>
                        <div class="p-3 bg-secondary-subtle rounded-3 border border-secondary-subtle text-center" style="font-size: 0.8rem;">
                            <i class="ri-information-line me-1"></i> ${data.disclaimer}
                        </div>
                    </div>
                `;
                flowRoot.insertAdjacentHTML('beforeend', html);
            }
            // Smooth scroll to the new content
            setTimeout(() => {
                chatMessages.scrollTo({
                    top: chatMessages.scrollHeight,
                    behavior: 'smooth'
                });
            }, 100);
        };

        renderStep1();
    }

    function showTypingIndicator() {
        const id = 'indicator-' + Date.now();
        const div = document.createElement('div');
        div.id = id;
        div.className = 'message-container ai-message mb-3';
        div.innerHTML = `
            <div class="d-flex align-items-start">
                <div class="avatar-box sm bg-primary text-white me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="ri-robot-2-fill fs-5"></i>
                </div>
                <div class="message-bubble p-3 rounded-3 bg-white shadow-sm border">
                    <div class="typing-indicator">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return id;
    }

    function removeTypingIndicator(id) {
        const indicator = document.getElementById(id);
        if (indicator) indicator.remove();
    }
});
</script>
@endsection
