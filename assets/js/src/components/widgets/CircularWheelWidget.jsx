import React from 'react';

/**
 * Circular Wheel Widget Component
 *
 * Renders a circular wheel with the following structure (outer to inner):
 * 1. Outer thin ring: Group titles (Spring, Summer, Fall, Winter)
 * 2. Images: Positioned between outer and middle rings
 * 3. Middle ring: Time slices (Morning, Afternoon, Evening, Night)
 * 4. Inner ring: Group types (Heels, Boots, Flats, Sneakers)
 * 5. Center circle: Icon/Logo
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

  // Calculate dimensions matching the reference image
  const wheelSizeValue = `${wheelSize.size}${wheelSize.unit}`;
  const gapSizeValue = gapSize.size;
  const centerCircleSizePercent = centerCircleSize.size;

  // Layer radii (from center outward) - ALL EQUAL WIDTH
  const centerRadius = (centerCircleSizePercent / 2) / 2; // Reduced to half (~7.5%)
  const gap = 0.5; // Small gap between layers

  // Calculate equal layer width for 4 layers (types, times, images, titles)
  const availableRadius = 50 - centerRadius - (gap * 5); // 5 gaps total
  const layerWidth = availableRadius / 4; // Equal width for all 4 layers

  // Layer 1: Inner ring (types)
  const innerRingInner = centerRadius + gap;
  const innerRingOuter = innerRingInner + layerWidth;

  // Layer 2: Middle ring (times)
  const middleRingInner = innerRingOuter + gap;
  const middleRingOuter = middleRingInner + layerWidth;

  // Layer 3: Image layer (dedicated space)
  const imageLayerInner = middleRingOuter + gap;
  const imageLayerOuter = imageLayerInner + layerWidth;

  // Layer 4: Outer ring (titles)
  const outerRingInner = imageLayerOuter + gap;
  const outerRingOuter = outerRingInner + layerWidth; // Should reach ~50

  // Calculate angles for each group
  const totalGroups = groups.length;
  const anglePerGroup = 360 / totalGroups;

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
   * Lighten a color by a percentage
   */
  const lightenColor = (hexColor, percent) => {
    const r = Number.parseInt(hexColor.substr(1, 2), 16);
    const g = Number.parseInt(hexColor.substr(3, 2), 16);
    const b = Number.parseInt(hexColor.substr(5, 2), 16);

    const newR = Math.floor(r + (255 - r) * (percent / 100));
    const newG = Math.floor(g + (255 - g) * (percent / 100));
    const newB = Math.floor(b + (255 - b) * (percent / 100));

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
   * Calculate rotation for text (outer ring - always upright)
   */
  const calculateRotation = (angle) => {
    // Adjust rotation so text is always readable (flip on bottom half)
    let rotation = angle;
    if (angle > 90 && angle < 270) {
      rotation = angle + 180;
    }
    return rotation;
  };

  /**
   * Calculate rotation for inner ring text (perpendicular to radius)
   */
  const calculateInnerRotation = (angle) => {
    // Text should follow the circle, perpendicular to radius
    // On the left side (90-270), add 180 to keep text upright
    let rotation = angle - 90; // Start perpendicular
    if (angle > 90 && angle < 270) {
      rotation = angle + 90; // Flip for left side
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

        {/* LAYER 1: Outer thin ring - Group titles (Spring, Summer, Fall, Winter) */}
        {groups.map((group, groupIndex) => {
          const startAngle = groupIndex * anglePerGroup;
          const endAngle = (groupIndex + 1) * anglePerGroup - gapSizeValue / 10;

          const midAngle = (startAngle + endAngle) / 2;
          const textRadius = (outerRingInner + outerRingOuter) / 2;
          const textPos = calculatePosition(midAngle, textRadius);
          const textRotation = calculateRotation(midAngle);

          return (
            <g key={`outer-${group.title}-${groupIndex}`}>
              {/* Outer ring segment */}
              <path
                d={createSegmentPath(startAngle, endAngle, outerRingInner, outerRingOuter)}
                fill={group.color}
                stroke="rgba(0,0,0,0.2)"
                strokeWidth="0.1"
                className="wheel-segment wheel-outer-segment"
              />

              {/* Group title text */}
              <text
                x={textPos.x}
                y={textPos.y}
                fill={groupTitleColor}
                fontSize="1.75"
                fontWeight="bold"
                textAnchor="middle"
                dominantBaseline="middle"
                transform={`rotate(${textRotation}, ${textPos.x}, ${textPos.y})`}
                className="wheel-segment-text"
              >
                {group.title.toUpperCase()}
              </text>
            </g>
          );
        })}

        {/* LAYER 2: Images in dedicated layer */}
        {groups.map((group, groupIndex) => {
          if (!group.image) return null;

          const startAngle = groupIndex * anglePerGroup;
          const endAngle = (groupIndex + 1) * anglePerGroup;
          const midAngle = (startAngle + endAngle) / 2;

          // Position images in dedicated image layer
          const imageRadius = (imageLayerInner + imageLayerOuter) / 2;
          const imagePos = calculatePosition(midAngle, imageRadius);

          return (
            <image
              key={`image-${groupIndex}`}
              href={group.image}
              x={imagePos.x - 2.5}
              y={imagePos.y - 2.5}
              width="5"
              height="5"
              className="wheel-segment-image"
              preserveAspectRatio="xMidYMid meet"
            />
          );
        })}

        {/* LAYER 3: Middle ring - Time slices (Morning, Afternoon, Evening, Night) */}
        {groups.map((group, groupIndex) => {
          const times = group.times || [];
          if (times.length === 0) return null;

          const groupStartAngle = groupIndex * anglePerGroup;
          const groupEndAngle = (groupIndex + 1) * anglePerGroup;
          const anglePerTime = (groupEndAngle - groupStartAngle) / times.length;

          // Use slightly lighter color for middle ring
          const middleColor = lightenColor(group.color, 10);

          return times.map((time, timeIndex) => {
            const startAngle = groupStartAngle + timeIndex * anglePerTime;
            const endAngle = groupStartAngle + (timeIndex + 1) * anglePerTime - gapSizeValue / 10;

            const midAngle = (startAngle + endAngle) / 2;
            const textRadius = (middleRingInner + middleRingOuter) / 2;
            const textPos = calculatePosition(midAngle, textRadius);
            const textRotation = calculateInnerRotation(midAngle);

            return (
              <g key={`middle-${groupIndex}-${timeIndex}`}>
                {/* Middle ring segment */}
                <path
                  d={createSegmentPath(startAngle, endAngle, middleRingInner, middleRingOuter)}
                  fill={middleColor}
                  stroke="rgba(0,0,0,0.2)"
                  strokeWidth="0.1"
                  className="wheel-segment wheel-middle-segment"
                />

                {/* Time text */}
                <text
                  x={textPos.x}
                  y={textPos.y}
                  fill={timeColor}
                  fontSize="1"
                  fontWeight="500"
                  textAnchor="middle"
                  dominantBaseline="middle"
                  transform={`rotate(${textRotation}, ${textPos.x}, ${textPos.y})`}
                  className="wheel-segment-text"
                >
                  {time.toUpperCase()}
                </text>
              </g>
            );
          });
        })}

        {/* LAYER 4: Inner ring - Group types (Heels, Boots, Flats, Sneakers) */}
        {groups.map((group, groupIndex) => {
          const startAngle = groupIndex * anglePerGroup;
          const endAngle = (groupIndex + 1) * anglePerGroup - gapSizeValue / 10;

          const midAngle = (startAngle + endAngle) / 2;
          const textRadius = (innerRingInner + innerRingOuter) / 2;
          const textPos = calculatePosition(midAngle, textRadius);
          const textRotation = calculateInnerRotation(midAngle);

          // Use darker color for inner ring
          const innerColor = darkenColor(group.color, 30);
          const typeLabel = group.type || '';

          return (
            <g key={`inner-${groupIndex}`}>
              {/* Inner ring segment - ONE per group */}
              <path
                d={createSegmentPath(startAngle, endAngle, innerRingInner, innerRingOuter)}
                fill={innerColor}
                stroke="rgba(0,0,0,0.2)"
                strokeWidth="0.1"
                className="wheel-segment wheel-inner-segment"
              />

              {/* Type text - ONE per group */}
              {typeLabel && (
                <text
                  x={textPos.x}
                  y={textPos.y}
                  fill={timeColor}
                  fontSize="1.2"
                  fontWeight="400"
                  textAnchor="middle"
                  dominantBaseline="middle"
                  transform={`rotate(${textRotation}, ${textPos.x}, ${textPos.y})`}
                  className="wheel-inner-segment-text"
                >
                  {typeLabel.toUpperCase()}
                </text>
              )}
            </g>
          );
        })}

        {/* LAYER 5: Center circle with icon/logo */}
        <circle
          cx="50"
          cy="50"
          r={centerRadius}
          className="wheel-center"
          fill="#000000"
          stroke="rgba(255,255,255,0.1)"
          strokeWidth="0.2"
        />

        {/* Center icon or text */}
        {centerIcon ? (
          <image
            href={centerIcon}
            x={50 - centerRadius}
            y={50 - centerRadius}
            width={centerRadius * 2}
            height={centerRadius * 2}
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
