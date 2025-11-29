document.addEventListener('DOMContentLoaded', function () {
    const summarizeBtn = document.getElementById('summarize-with-ai');
    const intakeNotes = document.getElementById('intake_notes');

    if (summarizeBtn && intakeNotes) {
        summarizeBtn.addEventListener('click', async () => {
            const notes = intakeNotes.value;
            if (!notes.trim()) {
                alert('Please enter some intake notes first.');
                return;
            }

            summarizeBtn.disabled = true;
            summarizeBtn.innerHTML = 'Summarizing...';

            try {
                const response = await fetch('api/summarize_notes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ notes: notes })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok.');
                }

                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                if (data.summary) {
                    // This is a simple example. We'll populate fields based on a hypothetical structured response.
                    // A real implementation would need to parse the summary more intelligently.
                    const summary = data.summary;
                    
                    // Example of populating fields:
                    if (summary.full_legal_name) {
                        document.getElementById('full_legal_name').value = summary.full_legal_name;
                    }
                    if (summary.email) {
                        document.getElementById('email').value = summary.email;
                    }
                     if (summary.primary_phone) {
                        document.getElementById('primary_phone').value = summary.primary_phone;
                    }
                    if (summary.primary_disability) {
                        document.getElementById('primary_disability').value = summary.primary_disability;
                    }
                     if (summary.support_needs_summary) {
                        document.getElementById('support_needs_summary').value = summary.support_needs_summary;
                    }
                    
                    alert('AI summarization complete. Please review the populated fields.');
                }

            } catch (error) {
                console.error('AI Summarization Error:', error);
                alert('An error occurred while summarizing the notes. Please check the console.');
            } finally {
                summarizeBtn.disabled = false;
                summarizeBtn.innerHTML = 'Summarize with AI';
            }
        });
    }
});