const cards = [
  {
    id: 1,
    title: "Cloud Computing",
    description: "Scalable cloud infrastructure for modern applications with high availability and global reach.",
    category: "technology",
    image: "https://picsum.photos/seed/cloud/400/300"
  },
  {
    id: 2,
    title: "UI/UX Design",
    description: "Human-centered design principles that create intuitive and delightful user experiences.",
    category: "design",
    image: "https://picsum.photos/seed/design/400/300"
  },
  {
    id: 3,
    title: "Startup Strategy",
    description: "Proven frameworks for launching and scaling successful business ventures.",
    category: "business",
    image: "https://picsum.photos/seed/startup/400/300"
  },
  {
    id: 4,
    title: "Artificial Intelligence",
    description: "Machine learning and AI solutions transforming industries and everyday life.",
    category: "technology",
    image: "https://picsum.photos/seed/ai/400/300"
  },
  {
    id: 5,
    title: "Brand Identity",
    description: "Crafting memorable brand identities that resonate with target audiences.",
    category: "design",
    image: "https://picsum.photos/seed/brand/400/300"
  },
  {
    id: 6,
    title: "E-Commerce",
    description: "Building and optimizing online stores for maximum conversion and revenue growth.",
    category: "business",
    image: "https://picsum.photos/seed/ecommerce/400/300"
  },
  {
    id: 7,
    title: "Cybersecurity",
    description: "Protecting digital assets with cutting-edge security protocols and threat detection.",
    category: "technology",
    image: "https://picsum.photos/seed/security/400/300"
  },
  {
    id: 8,
    title: "Motion Graphics",
    description: "Bringing stories to life through dynamic animation and visual effects.",
    category: "design",
    image: "https://picsum.photos/seed/motion/400/300"
  }
];

const cardGrid = document.getElementById("cardGrid");
const searchInput = document.getElementById("searchInput");
const filterSelect = document.getElementById("filterSelect");

function renderCards(filteredCards) {
  cardGrid.innerHTML = filteredCards
    .map(
      (card) => `
      <div class="card" data-id="${card.id}" data-category="${card.category}">
        <img class="card-image" src="${card.image}" alt="${card.title}" loading="lazy" />
        <div class="card-body">
          <span class="card-tag tag-${card.category}">${card.category}</span>
          <h3 class="card-title">${card.title}</h3>
          <p class="card-description">${card.description}</p>
          <button class="card-button">Learn More</button>
        </div>
      </div>
    `
    )
    .join("");
}

function filterCards() {
  const searchTerm = searchInput.value.toLowerCase();
  const category = filterSelect.value;
  const filtered = cards.filter((card) => {
    const matchesSearch =
      card.title.toLowerCase().includes(searchTerm) ||
      card.description.toLowerCase().includes(searchTerm);
    const matchesCategory = category === "all" || card.category === category;
    return matchesSearch && matchesCategory;
  });
  renderCards(filtered);
}

searchInput.addEventListener("input", filterCards);
filterSelect.addEventListener("change", filterCards);

renderCards(cards);
