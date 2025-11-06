import React from 'react';

/**
 * Circular Wheel Widget Component
 *
 * Renders a circular wheel with groups, times, and images
 */
const CircularWheelWidget = ({ widgetId, settings }) => {
  const {
    groups = [],
    centerIcon = '',
    centerText = '',
    wheelSize = { size: 600, unit: 'px' },
    ringWidth = { size: 25, unit: '%' },
    innerRingWidth = { size: 20, unit: '%' },
    gapSize = { size: 2, unit: 'px' },
    centerCircleSize = { size: 30, unit: '%' },
    groupTitleColor = '#ffffff',
    timeColor = '#ffffff',
    groupImageSize = { size: 60, unit: 'px' },
    centerIconSize = { size: 80, unit: 'px' },
  } = settings;

  // Calculate dimensions
  const wheelSizeValue = `${wheelSize.size}${wheelSize.unit}`;
  const ringWidthPercent = ringWidth.size;
  const innerRingWidthPercent = innerRingWidth.size;
  const centerCircleSizePercent = centerCircleSize.size;
  const gapSizeValue = gapSize.size;

  // Calculate angles for each group
  const totalGroups = groups.length;
  const anglePerGroup = 360 / totalGroups;

  /**
   * Calculate the color brightness and return appropriate text color
   */
  const getContrastColor = (hexColor) => {
    // Convert hex to RGB
    const r = Number.parseInt(hexColor.substr(1, 2), 16);
    const g = Number.parseInt(hexColor.substr(3, 2), 16);
    const b = Number.parseInt(hexColor.substr(5, 2), 16);

    // Calculate brightness
    const brightness = (r * 299 + g * 587 + b * 114) / 1000;

    return brightness > 128 ? '#000000' : '#ffffff';
  };

  /**
   * Darken a color by a percentage
   */
  const darkenColor = (hexColor, percent) => {
    const r = Number.parseInt(hexColor.substr(1, 2), 16);
    const g = Number.parseInt(hexColor.substr(3, 2), 16);
    const b = Number.parseInt(hexColor.substr(5, 2), 16);

    const newR = Math.floor(r * (1 - percent / 100));
    const newG = Math.floor(g * (1 - percent / 100));
    const newB = Math.floor(b * (1 - percent / 100));

    return `#${newR.toString(16).padStart(2, '0')}${newG.toString(16).padStart(2, '0')}${newB.toString(16).padStart(2, '0')}`;
  };

  /**
   * Create SVG path for a segment
   */
  const createSegmentPath = (startAngle, endAngle, innerRadius, outerRadius) => {
    const startAngleRad = ((startAngle - 90) * Math.PI) / 180;
    const endAngleRad = ((endAngle - 90) * Math.PI) / 180;

    const x1 = 50 + outerRadius * Math.cos(startAngleRad);
    const y1 = 50 + outerRadius * Math.sin(startAngleRad);
    const x2 = 50 + outerRadius * Math.cos(endAngleRad);
    const y2 = 50 + outerRadius * Math.sin(endAngleRad);
    const x3 = 50 + innerRadius * Math.cos(endAngleRad);
    const y3 = 50 + innerRadius * Math.sin(endAngleRad);
    const x4 = 50 + innerRadius * Math.cos(startAngleRad);
    const y4 = 50 + innerRadius * Math.sin(startAngleRad);

    const largeArcFlag = endAngle - startAngle > 180 ? 1 : 0;

    return `
      M ${x1} ${y1}
      A ${outerRadius} ${outerRadius} 0 ${largeArcFlag} 1 ${x2} ${y2}
      L ${x3} ${y3}
      A ${innerRadius} ${innerRadius} 0 ${largeArcFlag} 0 ${x4} ${y4}
      Z
    `;
  };

  /**
   * Calculate position for text or image
   */
  const calculatePosition = (angle, radius) => {
    const angleRad = ((angle - 90) * Math.PI) / 180;
    return {
      x: 50 + radius * Math.cos(angleRad),
      y: 50 + radius * Math.sin(angleRad),
    };
  };

  /**
   * Calculate rotation for text
   */
  const calculateRotation = (angle) => {
    // Adjust rotation so text is always readable
    let rotation = angle;
    if (angle > 90 && angle < 270) {
      rotation = angle + 180;
    }
    return rotation;
  };

  // Render the wheel
  return (
    <div
      className="philosofeet-circular-wheel"
      style={{ width: wheelSizeValue, height: wheelSizeValue }}
    >
      <svg viewBox="0 0 100 100" className="wheel-svg" style={{ width: '100%', height: '100%' }}>
        {/* Define filters for better visuals */}
        <defs>
          <filter id={`shadow-${widgetId}`} x="-50%" y="-50%" width="200%" height="200%">
            <feGaussianBlur in="SourceAlpha" stdDeviation="0.5" />
            <feOffset dx="0" dy="0.5" result="offsetblur" />
            <feComponentTransfer>
              <feFuncA type="linear" slope="0.3" />
            </feComponentTransfer>
            <feMerge>
              <feMergeNode />
              <feMergeNode in="SourceGraphic" />
            </feMerge>
          </filter>
        </defs>

        {/* Render outer ring segments (groups) */}
        {groups.map((group, index) => {
          const startAngle = index * anglePerGroup;
          const endAngle = (index + 1) * anglePerGroup - gapSizeValue / 5;
          const outerRadius = 50;
          const innerRadius = 50 - ringWidthPercent;

          const midAngle = (startAngle + endAngle) / 2;
          const textRadius = outerRadius - ringWidthPercent / 2;
          const textPos = calculatePosition(midAngle, textRadius);
          const textRotation = calculateRotation(midAngle);

          const imageRadius = outerRadius + 5; // Position images outside the ring
          const imagePos = calculatePosition(midAngle, imageRadius);

          return (
            <g key={`group-${index}`}>
              {/* Outer segment */}
              <path
                d={createSegmentPath(startAngle, endAngle, innerRadius, outerRadius)}
                fill={group.color}
                stroke="rgba(0,0,0,0.1)"
                strokeWidth="0.2"
                filter={`url(#shadow-${widgetId})`}
                className="wheel-segment"
              />

              {/* Group title text */}
              <text
                x={textPos.x}
                y={textPos.y}
                fill={groupTitleColor || getContrastColor(group.color)}
                fontSize="3"
                fontWeight="bold"
                textAnchor="middle"
                dominantBaseline="middle"
                transform={`rotate(${textRotation}, ${textPos.x}, ${textPos.y})`}
                className="wheel-segment-text"
              >
                {group.title.toUpperCase()}
              </text>

              {/* Group image */}
              {group.image && (
                <image
                  href={group.image}
                  x={imagePos.x - 3}
                  y={imagePos.y - 3}
                  width="6"
                  height="6"
                  className="wheel-segment-image"
                  preserveAspectRatio="xMidYMid meet"
                />
              )}
            </g>
          );
        })}

        {/* Render inner ring segments (times) */}
        {groups.map((group, groupIndex) => {
          const times = group.times || [];
          const groupStartAngle = groupIndex * anglePerGroup;
          const groupEndAngle = (groupIndex + 1) * anglePerGroup;
          const anglePerTime = (groupEndAngle - groupStartAngle) / times.length;

          const outerRadius = 50 - ringWidthPercent - 1;
          const innerRadius = outerRadius - innerRingWidthPercent;

          // Darker shade for inner ring
          const innerColor = darkenColor(group.color, 20);

          return times.map((time, timeIndex) => {
            const startAngle = groupStartAngle + timeIndex * anglePerTime;
            const endAngle = groupStartAngle + (timeIndex + 1) * anglePerTime - gapSizeValue / 5;

            const midAngle = (startAngle + endAngle) / 2;
            const textRadius = outerRadius - innerRingWidthPercent / 2;
            const textPos = calculatePosition(midAngle, textRadius);
            const textRotation = calculateRotation(midAngle);

            return (
              <g key={`time-${groupIndex}-${timeIndex}`}>
                {/* Inner segment */}
                <path
                  d={createSegmentPath(startAngle, endAngle, innerRadius, outerRadius)}
                  fill={innerColor}
                  stroke="rgba(0,0,0,0.1)"
                  strokeWidth="0.2"
                  filter={`url(#shadow-${widgetId})`}
                  className="wheel-inner-segment"
                />

                {/* Time text */}
                <text
                  x={textPos.x}
                  y={textPos.y}
                  fill={timeColor || getContrastColor(innerColor)}
                  fontSize="2"
                  textAnchor="middle"
                  dominantBaseline="middle"
                  transform={`rotate(${textRotation}, ${textPos.x}, ${textPos.y})`}
                  className="wheel-inner-segment-text"
                >
                  {time.toUpperCase()}
                </text>
              </g>
            );
          });
        })}

        {/* Center circle */}
        <circle
          cx="50"
          cy="50"
          r={centerCircleSizePercent / 2}
          className="wheel-center"
          fill="#000000"
          filter={`url(#shadow-${widgetId})`}
        />

        {/* Center icon or text */}
        {centerIcon ? (
          <image
            href={centerIcon}
            x={50 - centerCircleSizePercent / 4}
            y={50 - centerCircleSizePercent / 4}
            width={centerCircleSizePercent / 2}
            height={centerCircleSizePercent / 2}
            className="wheel-center-icon"
            preserveAspectRatio="xMidYMid meet"
          />
        ) : centerText ? (
          <text
            x="50"
            y="50"
            fill="#ffffff"
            fontSize="4"
            fontWeight="bold"
            textAnchor="middle"
            dominantBaseline="middle"
            className="wheel-center-text"
          >
            {centerText}
          </text>
        ) : null}
      </svg>
    </div>
  );
};

export default CircularWheelWidget;
