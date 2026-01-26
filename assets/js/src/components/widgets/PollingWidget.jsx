import React, { useState, useEffect } from 'react';

const PollingWidget = ({ settings }) => {
  const { pollId, layout } = settings;
  const [poll, setPoll] = useState(null);
  const [loading, setLoading] = useState(true);
  const [voted, setVoted] = useState(false);
  const [error, setError] = useState(null);
  const [voting, setVoting] = useState(false);

  useEffect(() => {
    if (!pollId) {
      setLoading(false);
      return;
    }

    const fetchPoll = async () => {
      try {
        const response = await fetch(`${window.philosofeetCore.restUrl}philosofeet/v1/poll/${pollId}`);
        if (!response.ok) {
          throw new Error('Failed to fetch poll');
        }
        const data = await response.json();
        setPoll(data);
      } catch (err) {
        console.error('Error fetching poll:', err);
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchPoll();
  }, [pollId]);

  const handleVote = async (index) => {
    if (voting || voted) return;
    setVoting(true);

    try {
      const response = await fetch(`${window.philosofeetCore.restUrl}philosofeet/v1/vote`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': window.philosofeetCore.nonce
        },
        body: JSON.stringify({
          poll_id: pollId,
          option_index: index
        })
      });

      if (!response.ok) {
        throw new Error('Vote failed');
      }

      const result = await response.json();
      
      // Update poll data with new results
      if (result.results && poll) {
          setPoll({
              ...poll,
              options: result.results
          });
      }
      
      setVoted(true);
    } catch (err) {
      console.error('Error voting:', err);
      // specific error handling if needed
    } finally {
      setVoting(false);
    }
  };

  if (loading) {
    return <div className="philosofeet-poll-loading">Loading poll...</div>;
  }

  if (error) {
     return <div className="philosofeet-poll-error">Error: {error}</div>;
  }
  
  if (!poll) {
      return <div className="philosofeet-poll-empty">Please select a poll.</div>;
  }

  const containerClass = `philosofeet-poll-container ${layout === 'row' ? 'layout-row' : 'layout-column'}`;
  
  // Calculate total votes for percentage
  const totalVotes = poll.options.reduce((acc, opt) => acc + (Number.parseInt(opt.votes) || 0), 0);

  return (
    <div className={containerClass}>
      {/* <h3 className="philosofeet-poll-title">{poll.title}</h3> */}
      
      <div className="philosofeet-poll-options" style={{ display: 'flex', flexDirection: layout === 'row' ? 'row' : 'column', flexWrap: 'wrap' }}>
        {!voted ? (
            poll.options.map((option, index) => (
            <button 
                key={index} 
                className="philosofeet-poll-button"
                onClick={() => handleVote(index)}
                disabled={voting}
                type="button"
            >
                {option.label}
            </button>
            ))
        ) : (
            <div className="philosofeet-poll-results" style={{ width: '100%' }}>
                {poll.options.map((option, index) => {
                    const votes = Number.parseInt(option.votes) || 0;
                    const percentage = totalVotes > 0 ? Math.round((votes / totalVotes) * 100) : 0;
                    
                    return (
                        <div key={index} className="philosofeet-poll-result-item" style={{ marginBottom: '10px' }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '5px' }}>
                                <span className="result-label">{option.label}</span>
                                <span className="result-percentage">{percentage}% ({votes})</span>
                            </div>
                            <div className="result-bar-container" style={{ background: '#eee', height: '10px', borderRadius: '5px', overflow: 'hidden' }}>
                                <div 
                                    className="result-bar-fill" 
                                    style={{ 
                                        width: `${percentage}%`, 
                                        background: 'var(--e-global-color-primary, #61ce70)', 
                                        height: '100%',
                                        transition: 'width 0.5s ease-out'
                                    }}
                                />
                            </div>
                        </div>
                    );
                })}
                <div className="philosofeet-poll-thankyou" style={{ marginTop: '15px', fontStyle: 'italic' }}>
                    Thank you for voting!
                </div>
            </div>
        )}
      </div>
    </div>
  );
};

export default PollingWidget;
