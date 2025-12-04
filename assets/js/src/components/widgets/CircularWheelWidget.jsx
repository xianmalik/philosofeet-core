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
  const centerRadius = centerCircleSizePercent / 2 / 2; // Reduced to half (~7.5%)
  const gap = 0.5; // Small gap between layers

  // Calculate equal layer width for 4 layers (types, times, images, titles)
  const availableRadius = 50 - centerRadius - gap * 5; // 5 gaps total
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
   * First half (0-180°): text reads from inside to outside (radially outward)
   * Second half (180-360°): text reads from outside to inside (radially inward)
   */
  const calculateInnerRotation = (angle) => {
    // Normalize angle to 0-360
    const normalizedAngle = angle % 360;

    // First half (top): text perpendicular, reading outward (angle - 90)
    // Second half (bottom): text perpendicular, reading inward (angle + 90)
    let rotation = normalizedAngle - 90;

    // Second half (180-360): flip to read from outside to inside
    if (normalizedAngle >= 180 && normalizedAngle < 360) {
      rotation = normalizedAngle + 90;
    }
    return rotation;
  };

  /**
   * Create gradient ID for a group
   */
  const getGradientId = (groupIndex) => `gradient-${widgetId}-${groupIndex}`;

  /**
   * Get fill reference for a group (either gradient URL or solid color with opacity)
   */
  const getGroupFill = (group, groupIndex) => {
    if (group.background?.type === 'gradient') {
      return `url(#${getGradientId(groupIndex)})`;
    }

    // Solid color with opacity
    const color = group.background?.color || group.color || '#8B4513';
    const opacity = group.background?.opacity ?? 1;

    // Convert hex to rgba
    const r = Number.parseInt(color.slice(1, 3), 16);
    const g = Number.parseInt(color.slice(3, 5), 16);
    const b = Number.parseInt(color.slice(5, 7), 16);

    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
  };

  // Render the wheel
  return (
    <div
      className="philosofeet-circular-wheel"
      style={{ width: wheelSizeValue, height: wheelSizeValue }}
    >
      <svg viewBox="0 0 100 100" className="wheel-svg" style={{ width: '100%', height: '100%' }}>
        {/* Define filters and gradients */}
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

          {/* Define gradients for groups */}
          {groups.map((group, groupIndex) => {
            if (group.background?.type !== 'gradient') return null;

            const {
              gradient_type = 'linear',
              gradient_angle = 180,
              gradient_position = 'center center',
              color = '#8B4513',
              color_b = '#000000',
              color_c = null,
              color_stop = 0,
              color_b_stop = 100,
              color_c_stop = 50,
              opacity = 1,
            } = group.background;

            // Helper to convert hex to rgba
            const hexToRgba = (hex, alpha) => {
              const r = Number.parseInt(hex.slice(1, 3), 16);
              const g = Number.parseInt(hex.slice(3, 5), 16);
              const b = Number.parseInt(hex.slice(5, 7), 16);
              return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            };

            if (gradient_type === 'radial') {
              // Parse gradient position (e.g., "center center" -> cx="50%" cy="50%")
              const [fx = 'center', fy = 'center'] = gradient_position.split(' ');
              const getCord = (val) => {
                if (val === 'center') return '50%';
                if (val === 'left' || val === 'top') return '0%';
                if (val === 'right' || val === 'bottom') return '100%';
                return val;
              };

              return (
                <radialGradient
                  key={getGradientId(groupIndex)}
                  id={getGradientId(groupIndex)}
                  cx={getCord(fx)}
                  cy={getCord(fy)}
                >
                  <stop offset={`${color_stop}%`} stopColor={hexToRgba(color, opacity)} />
                  {color_c && (
                    <stop offset={`${color_c_stop}%`} stopColor={hexToRgba(color_c, opacity)} />
                  )}
                  <stop offset={`${color_b_stop}%`} stopColor={hexToRgba(color_b, opacity)} />
                </radialGradient>
              );
            }

            // Linear gradient
            // Convert angle to SVG gradient vector
            const angleRad = ((gradient_angle - 90) * Math.PI) / 180;
            const x1 = 50 - 50 * Math.cos(angleRad);
            const y1 = 50 - 50 * Math.sin(angleRad);
            const x2 = 50 + 50 * Math.cos(angleRad);
            const y2 = 50 + 50 * Math.sin(angleRad);

            return (
              <linearGradient
                key={getGradientId(groupIndex)}
                id={getGradientId(groupIndex)}
                x1={`${x1}%`}
                y1={`${y1}%`}
                x2={`${x2}%`}
                y2={`${y2}%`}
              >
                <stop offset={`${color_stop}%`} stopColor={hexToRgba(color, opacity)} />
                {color_c && (
                  <stop offset={`${color_c_stop}%`} stopColor={hexToRgba(color_c, opacity)} />
                )}
                <stop offset={`${color_b_stop}%`} stopColor={hexToRgba(color_b, opacity)} />
              </linearGradient>
            );
          })}
        </defs>

        {/* LAYER 1: Outer thin ring - Group titles (Spring, Summer, Fall, Winter) */}
        {groups.map((group, groupIndex) => {
          const startAngle = groupIndex * anglePerGroup;
          const endAngle = (groupIndex + 1) * anglePerGroup - gapSizeValue / 10;

          const textRadius = (outerRingInner + outerRingOuter) / 2;

          // Create circular path for text
          const textPathId = `text-path-${widgetId}-${groupIndex}`;

          // Calculate arc path for the text to follow
          const startAngleRad = ((startAngle - 90) * Math.PI) / 180;
          const endAngleRad = ((endAngle - 90) * Math.PI) / 180;
          const x1 = 50 + textRadius * Math.cos(startAngleRad);
          const y1 = 50 + textRadius * Math.sin(startAngleRad);
          const x2 = 50 + textRadius * Math.cos(endAngleRad);
          const y2 = 50 + textRadius * Math.sin(endAngleRad);
          const largeArcFlag = endAngle - startAngle > 180 ? 1 : 0;

          // Reverse path for bottom half to keep text upright
          const isBottomHalf = startAngle > 90 && startAngle < 270;
          const textPath = isBottomHalf
            ? `M ${x2} ${y2} A ${textRadius} ${textRadius} 0 ${largeArcFlag} 0 ${x1} ${y1}`
            : `M ${x1} ${y1} A ${textRadius} ${textRadius} 0 ${largeArcFlag} 1 ${x2} ${y2}`;

          return (
            <g key={`outer-${group.title}-${groupIndex}`}>
              {/* Outer ring segment */}
              <path
                d={createSegmentPath(startAngle, endAngle, outerRingInner, outerRingOuter)}
                fill={getGroupFill(group, groupIndex)}
                stroke="rgba(0,0,0,0.2)"
                strokeWidth="0.1"
                className="wheel-segment wheel-outer-segment"
              />

              {/* Define circular path for text */}
              <defs>
                <path id={textPathId} d={textPath} fill="none" />
              </defs>

              {/* Group title text following the arc */}
              {group?.title && (
                <text
                  fill={groupTitleColor}
                  fontSize="1.75"
                  fontWeight="bold"
                  textAnchor="middle"
                  dominantBaseline="middle"
                  className="wheel-segment-text"
                >
                  <textPath href={`#${textPathId}`} startOffset="50%">
                    {group.title.toUpperCase()}
                  </textPath>
                </text>
              )}
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
              x={imagePos.x - 5}
              y={imagePos.y - 5}
              width="10"
              height="10"
              className="wheel-segment-image"
              preserveAspectRatio="xMidYMid meet"
            />
          );
        })}

        {/* LAYER 3: Middle ring - Time slices (Morning, Afternoon, Evening, Night) */}
        {groups.map((group, groupIndex) => {
          if (!group) return null;
          const times = Array.isArray(group.times) ? group.times : [];
          if (times.length === 0) return null;

          const groupStartAngle = groupIndex * anglePerGroup;
          const groupEndAngle = (groupIndex + 1) * anglePerGroup;
          const anglePerTime = (groupEndAngle - groupStartAngle) / times.length;

          return times.map((time, timeIndex) => {
            if (!time) return null;
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
                  fill={getGroupFill(group, groupIndex)}
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
                  {typeof time === 'string' ? time.toUpperCase() : time}
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

          const typeLabel = group.type || '';

          return (
            <g key={`inner-${groupIndex}`}>
              {/* Inner ring segment - ONE per group */}
              <path
                d={createSegmentPath(startAngle, endAngle, innerRingInner, innerRingOuter)}
                fill={getGroupFill(group, groupIndex)}
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
            x={50 - ((centerIconSize.size / wheelSize.size) * 100) / 2}
            y={50 - ((centerIconSize.size / wheelSize.size) * 100) / 2}
            width={(centerIconSize.size / wheelSize.size) * 100}
            height={(centerIconSize.size / wheelSize.size) * 100}
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
