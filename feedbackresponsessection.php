<div class="recent-feedback-responses">
                <h2>Your Feedback Responses</h2>
                <?php if ($recent_responses->num_rows > 0): ?>
                    <?php while($response = $recent_responses->fetch_assoc()): ?>
                        <div class="response-item">
                            <div class="response-header">
                                <div class="response-metadata">
                                    <strong class="response-category"><?= htmlspecialchars($response['category']) ?></strong>
                                    <span class="response-type">
                                        <?= $response['response_type'] ? htmlspecialchars($response['response_type']) : 'Response' ?>
                                    </span>
                                </div>
                                <div class="response-date"><?= date('M j, Y', strtotime($response['created_at'])) ?></div>
                            </div>
                            <div class="response-content">
                                <?= htmlspecialchars(
                                    strlen($response['response_text']) > 150 ? 
                                    substr($response['response_text'], 0, 150) . '...' : 
                                    $response['response_text']
                                ) ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No responses to your feedback yet.</p>
                    <a href="submit_feedback.php" class="view-all-btn">Submit Feedback</a>
                <?php endif; ?>
                <a href="view_response.php" class="view-all-btn">View All Responses</a>
            </div>