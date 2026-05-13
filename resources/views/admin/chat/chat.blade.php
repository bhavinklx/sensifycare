<!-- Chat Modal -->
<div class="modal fade" id="aiChatModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="aiChatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title d-flex align-items-center" id="aiChatModalLabel">
                    <i class="ri-robot-2-line me-2 fs-4"></i> Sensify Care AI Assistant
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="d-flex flex-column" style="height: 500px;">
                    <!-- Chat Messages Area -->
                    <div id="chatMessages" class="flex-grow-1 p-3 overflow-auto bg-light" style="background-color: #f8f9fa;">
                        <div class="message-container ai-message mb-3">
                            <div class="d-flex align-items-start">
                                <div class="avatar-box sm bg-primary text-white me-2 rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-robot-2-fill"></i>
                                </div>
                                <div class="message-bubble p-2 rounded-3 bg-white shadow-sm border" style="max-width: 80%;">
                                    Hello! How can I help you today? You can ask me health questions or upload a medical report for analysis.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="p-3 border-top bg-white">
                        <form id="aiChatForm">
                            <div class="d-flex flex-column gap-2">
                                <!-- Top Bar: Language & Info -->
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="small fw-bold text-muted mb-0"><i class="ri-translate-2"></i> Language:</label>
                                        <select id="chatLanguage" class="form-select form-select-sm border-0 bg-light shadow-none" style="width: auto; cursor: pointer;">
                                            <option value="en">English</option>
                                            <option value="hi">Hindi</option>
                                            <option value="gu">Gujarati</option>
                                        </select>
                                    </div>
                                    <div id="fileStatus" class="small text-muted d-none">
                                        <i class="ri-file-check-line text-success"></i> <span id="fileNameDisplay"></span>
                                        <button type="button" id="removeFileBtn" class="btn btn-link btn-sm text-danger p-0 ms-1" style="text-decoration: none;"><i class="ri-close-circle-fill"></i></button>
                                    </div>
                                </div>

                                <!-- Input Bar: Attach | Textarea | Send -->
                                <div class="d-flex align-items-end gap-2">
                                    <!-- Attach Icon -->
                                    <div class="mb-1">
                                        <label for="chatFile" class="btn btn-light border-0 rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; cursor: pointer; transition: all 0.2s;" data-bs-toggle="tooltip" title="Attach Medical Report">
                                            <i class="ri-attachment-2 font-bold text-primary fs-5"></i>
                                            <input type="file" id="chatFile" class="d-none" accept=".jpg,.jpeg,.png,.pdf,.xlsx,.xls">
                                        </label>
                                    </div>

                                    <!-- Textarea -->
                                    <div class="flex-grow-1">
                                        <textarea id="chatQuestion" class="form-control shadow-none border-light-subtle" rows="1" placeholder="Type your question..." style="resize: none; border-radius: 20px; transition: all 0.3s; padding: 10px 15px; min-height: 44px;"></textarea>
                                    </div>

                                    <!-- Send Button -->
                                    <div class="mb-1">
                                        <button type="submit" id="sendBtn" class="btn btn-primary d-flex align-items-center justify-content-center shadow rounded-circle" style="width: 44px; height: 44px; transition: transform 0.2s;">
                                            <i class="ri-send-plane-2-fill fs-5"></i>
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

<style>
    .message-bubble {
        word-wrap: break-word;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    .user-message .d-flex {
        flex-direction: row-reverse;
    }
    .user-message .message-bubble {
        background-color: #007bff !important;
        color: white !important;
        border: none !important;
        margin-left: auto;
        margin-right: 0.5rem;
        border-radius: 15px 15px 0 15px !important;
    }
    .ai-message .message-bubble {
        background-color: #f0f2f5 !important;
        border: none !important;
        border-radius: 15px 15px 15px 0 !important;
        color: #1c1e21 !important;
    }
    .avatar-box.bg-primary {
        background-color: #007bff !important;
    }
    #chatQuestion:focus {
        border-color: #007bff !important;
        background-color: #fff !important;
    }
    #sendBtn:hover {
        transform: scale(1.05);
    }
    #sendBtn:active {
        transform: scale(0.95);
    }
    .btn-light:hover {
        background-color: #e9ecef !important;
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
        margin: 0 1px;
        background-color: #9E9E9E;
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
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .hook-button {
        background: #e7f3ff;
        color: #007bff;
        border: 1px dashed #007bff;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
    }
    .hook-button:hover {
        background: #007bff;
        color: white;
        border-style: solid;
    }
    .status-badge {
        padding: 4px 10px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: bold;
        text-transform: uppercase;
    }
    .status-yellow { background: #fff3cd; color: #856404; }
    .status-green { background: #d4edda; color: #155724; }
    .status-red { background: #f8d7da; color: #721c24; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('aiChatForm');
    const chatMessages = document.getElementById('chatMessages');
    const chatQuestion = document.getElementById('chatQuestion');
    const chatLanguage = document.getElementById('chatLanguage');
    const fileInput = document.getElementById('chatFile');

    // Auto-resize textarea
    chatQuestion.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        if (this.scrollHeight > 150) {
            this.style.overflowY = 'auto';
            this.style.height = '150px';
        } else {
            this.style.overflowY = 'hidden';
        }
    });

    // Handle Enter to Send
    chatQuestion.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.requestSubmit(); // Triggers the submit event on the form
        }
    });

    const fileStatus = document.getElementById('fileStatus');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const removeFileBtn = document.getElementById('removeFileBtn');
    const sendBtn = document.getElementById('sendBtn');

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
            userMsgText = (question ? `<strong>Question:</strong> ${question}<br>` : '') + `<div class="mt-1 p-2 bg-primary-subtle border border-primary-subtle rounded-2 text-primary small"><i class="ri-file-search-line"></i> Analyzing Report: <em>${fileInput.files[0].name}</em></div>`;
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
            fileStatus.classList.add('d-none');
            fileInput.value = '';
            chatQuestion.style.height = 'auto'; // Reset height
            setTimeout(() => chatQuestion.focus(), 100);
        }
    });

    function appendMessage(role, text) {
        const div = document.createElement('div');
        div.className = `message-container ${role}-message mb-3`;
        
        const icon = role === 'ai' ? 'ri-robot-2-fill' : 'ri-user-fill';
        const bg = role === 'ai' ? 'bg-primary' : 'bg-secondary';

        div.innerHTML = `
            <div class="d-flex align-items-start">
                <div class="avatar-box sm ${bg} text-white me-2 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="${icon}"></i>
                </div>
                <div class="message-bubble p-3 rounded-3 shadow-sm border" style="max-width: 85%;">
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
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-primary"><i class="ri-survey-line"></i> Step 1: Summary</span>
                        <span class="status-badge ${statusClass}">${step1.status_label}</span>
                    </div>
                    <ul class="ps-3 mb-2 small">
                        ${step1.takeaways.map(t => `<li class="mb-1">${t}</li>`).join('')}
                    </ul>
                    <div class="p-2 bg-light rounded-2 border-start border-success border-3 small mb-2">
                        <i class="ri-shield-check-line text-success"></i> ${step1.reassurance}
                    </div>
                    ${step1.hook ? `<button class="hook-button" onclick="window.revealStep(2)">${step1.hook} <i class="ri-arrow-right-s-line"></i></button>` : ''}
                </div>
            `;
            flowRoot.innerHTML = html;
        }

        window.revealStep = function(stepNum) {
            const currentHook = flowRoot.querySelector('.hook-button');
            if (currentHook) currentHook.remove();

            if (stepNum === 2) {
                const step2 = data.step2;
                const html = `
                    <div class="progressive-step mt-3 pt-3 border-top" id="step2">
                        <div class="fw-bold text-primary mb-2"><i class="ri-body-scan-line"></i> Step 2: System Breakdown</div>
                        ${step2.modules.map(m => `
                            <div class="mb-3">
                                <div class="small fw-bold text-dark">${m.category}</div>
                                <div class="small text-muted mt-1">${m.interpretation}</div>
                                <div class="small font-italic text-secondary mt-1 border-start ps-2" style="border-width: 2px !important;">${m.comparison}</div>
                            </div>
                        `).join('')}
                        ${step2.hook ? `<button class="hook-button" onclick="window.revealStep(3)">${step2.hook} <i class="ri-arrow-right-s-line"></i></button>` : ''}
                    </div>
                `;
                flowRoot.insertAdjacentHTML('beforeend', html);
            } else if (stepNum === 3) {
                const step3 = data.step3;
                const html = `
                    <div class="progressive-step mt-3 pt-3 border-top" id="step3">
                        <div class="fw-bold text-primary mb-2"><i class="ri-leaf-line"></i> Step 3: Advice & Roadmap</div>
                        <div class="row g-2">
                            <div class="col-12 mb-2">
                                <div class="p-2 bg-info-subtle rounded-2 border border-info-subtle">
                                    <div class="small fw-bold text-info-emphasis"><i class="ri-restaurant-line"></i> Diet</div>
                                    <div class="small text-dark">${step3.lifestyle.diet}</div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="p-2 bg-success-subtle rounded-2 border border-success-subtle">
                                    <div class="small fw-bold text-success-emphasis"><i class="ri-run-line"></i> Activity</div>
                                    <div class="small text-dark">${step3.lifestyle.activity}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 p-2 bg-warning-subtle rounded-2 border border-warning-subtle">
                            <div class="small fw-bold text-warning-emphasis"><i class="ri-map-pin-user-line"></i> Medical Roadmap</div>
                            <div class="small text-dark mb-1">${step3.medical_roadmap.specialist}</div>
                            <div class="small text-muted"><strong>Checklist:</strong> ${step3.medical_roadmap.checklist}</div>
                        </div>
                        ${step3.hook ? `<button class="hook-button" onclick="window.revealStep(4)">${step3.hook} <i class="ri-arrow-right-s-line"></i></button>` : ''}
                    </div>
                `;
                flowRoot.insertAdjacentHTML('beforeend', html);
            } else if (stepNum === 4) {
                const step4 = data.step4;
                const html = `
                    <div class="progressive-step mt-3 pt-3 border-top" id="step4">
                        <div class="fw-bold text-primary mb-2"><i class="ri-flask-line"></i> Step 4: Next Steps</div>
                        <div class="small mb-2"><strong>Reflex Testing:</strong> ${step4.reflex_testing}</div>
                        <div class="small mb-3"><strong>Follow-up:</strong> ${step4.follow_up_timeline}</div>
                        <div class="d-flex gap-2">
                            ${step4.cta_buttons.map(btn => `<button class="btn btn-sm btn-outline-primary rounded-pill px-3">${btn}</button>`).join('')}
                        </div>
                        <div class="mt-3 p-2 bg-secondary-subtle rounded-2 border text-center" style="font-size: 0.7rem;">
                            <i class="ri-information-line"></i> ${data.disclaimer}
                        </div>
                    </div>
                `;
                flowRoot.insertAdjacentHTML('beforeend', html);
            }
            chatMessages.scrollTop = chatMessages.scrollHeight;
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
                <div class="avatar-box sm bg-primary text-white me-2 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="ri-robot-2-fill"></i>
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
