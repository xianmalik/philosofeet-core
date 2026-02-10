import React, { useState, useEffect } from 'react';

const PollingWidget = ({ settings }) => {
  const { pollId, layout } = settings;
  const [poll, setPoll] = useState(null);
  const [loading, setLoading] = useState(true);
  const [voted, setVoted] = useState(false);
  const [error, setError] = useState(null);
  const [voting, setVoting] = useState(false);
  const [selectedOption, setSelectedOption] = useState(null);

  useEffect(() => {
    if (!pollId) {
      setLoading(false);
      return;
    }

    const fetchPoll = async () => {
      try {
        const response = await fetch(
          `${window.philosofeetCore.restUrl}philosofeet/v1/poll/${pollId}`
        );
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
    setSelectedOption(index);

    try {
      const response = await fetch(`${window.philosofeetCore.restUrl}philosofeet/v1/vote`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': window.philosofeetCore.nonce,
        },
        body: JSON.stringify({
          poll_id: pollId,
          option_index: index,
        }),
      });

      if (!response.ok) {
        throw new Error('Vote failed');
      }

      const result = await response.json();

      // Update poll data with new results
      if (result.results && poll) {
        setPoll({
          ...poll,
          options: result.results,
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

      <div
        className="philosofeet-poll-options"
        style={{
          display: 'flex',
          flexDirection: layout === 'row' ? 'row' : 'column',
          flexWrap: 'wrap',
        }}
      >
        {poll.options.map((option, index) => {
          const isSelected = selectedOption === index;
          return (
            <button
              key={index}
              className={`philosofeet-poll-button ${isSelected ? 'selected' : ''}`}
              onClick={() => handleVote(index)}
              disabled={voting || voted}
              type="button"
              style={{
                position: 'relative',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                gap: '8px',
                backgroundColor: voted && isSelected ? '#ffffff' : undefined,
                color: voted && isSelected ? '#000000' : undefined,
              }}
            >
              {/* Checkmark icon for selected option */}
              {voted && isSelected && (
                <span
                  className="poll-checkmark"
                  style={{
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    color: 'inherit',
                  }}
                >
                  <svg
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="3"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  >
                    <polyline points="20 6 9 17 4 12" />
                  </svg>
                </span>
              )}
              {option.label}
            </button>
          );
        })}
      </div>
    </div>
  );
};

export default PollingWidget;
