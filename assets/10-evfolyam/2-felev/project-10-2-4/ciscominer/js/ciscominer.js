var preLoad = {
  minebutton: null,
  balance: null,
  header: null,
  workers: null,
  items: ["ciscominer", "ciscoescavator", "ciscofactory", "ciscoscientist"],
  upgrades: ["upgrade1", "upgrade2", "upgrade3", "upgrade4", "upgrade5"],
  itemprices: [30, 200, 1000, 10000],
  itemquantity: [0, 0, 0, 0],
  upgradeprices: [2000, 5000, 10000, 20000, 50000],
  multipliers: [1, 1, 1, 1, 1],
  pricechange: [4, 50, 200, 5000],
  ciscocoin: 0,
  ciscopersecond: 0,
  totalspent: 0,
  newsarr: [
    "Cisco.",
    "gyak (//balu) (H:) Szeba",
    "Nem lopott!",
    "A cisco részvény árfolyama 48,35$",
    "Cisco Packer Tracer 8.2.1",
    "Kattintson azokra a Cisco termékekre!",
    "A Cisco központja Burkina Fasóban található",
    "Kinek kellenek barátok, ha rendelkezik Cisco Packet Tracer 8.3-mal?",
    "Packet Tracer: Mert a valódi útválasztók megsütése drága.",
    "Kinek van szüksége labortársra? Cisco Packet Tracered van.",
  ],

  begin: function () {
    // GET ELEMENTS
    preLoad.minebutton = document.getElementById("minebutton");
    preLoad.balance = document.getElementById("balance");
    preLoad.header = document.getElementById("header");
    preLoad.workers = document.getElementById("workers");

    // ADD EVENT LISTENER TO MINE BUTTON
    preLoad.minebutton.addEventListener("click", mine);

    getItems();
  },
};

document.addEventListener('DOMContentLoaded', function () {
  var hamburger = document.getElementById('hamburger1');
  var isActive = false;

  hamburger.addEventListener('click', function () {
    if (!isActive) {
      document.getElementById('top-line1').style.animation = 'down-rotate 0.6s ease-out both';
      document.getElementById('bottom-line1').style.animation = 'up-rotate 0.6s ease-out both';
      document.getElementById('middle-line1').style.animation = 'hide 0.6s ease-out forwards';
      
      document.getElementById('balmenu').style.display = 'block';
      document.getElementById('column1').style.backgroundColor = '#303030';

    } else {
      document.getElementById('top-line1').style.animation = '';
      document.getElementById('bottom-line1').style.animation = '';
      document.getElementById('middle-line1').style.animation = '';
      
      document.getElementById('balmenu').style.display = 'none';
      document.getElementById('column1').style.backgroundColor = '#a0a0a0';
    }
    isActive = !isActive;
  });
});

document.addEventListener('DOMContentLoaded', function () {
  var hamburger = document.getElementById('hamburger2');
  var isActive = false;

  hamburger.addEventListener('click', function () {
    if (!isActive) {
      document.getElementById('top-line2').style.animation = 'down-rotate 0.6s ease-out both';
      document.getElementById('bottom-line2').style.animation = 'up-rotate 0.6s ease-out both';
      document.getElementById('middle-line2').style.animation = 'hide 0.6s ease-out forwards';
      
      document.getElementById('jobbmenu').style.display = 'block';
      document.getElementById('column2').style.backgroundColor = '#303030';
      
    } else {
      document.getElementById('top-line2').style.animation = '';
      document.getElementById('bottom-line2').style.animation = '';
      document.getElementById('middle-line2').style.animation = '';

      document.getElementById('jobbmenu').style.display = 'none';
      document.getElementById('column2').style.backgroundColor = '#a0a0a0';
    }
    isActive = !isActive;
  });
});


function getItems() {
  // GET MARKETPLACE ITEMS AND ADD EVENT LISTENERS
  for (let i = 0; i < preLoad.items.length; i++) {
    item = document.getElementById(preLoad.items[i]);
    item.addEventListener("click", handleBuy);
  }
  // GET UPGRADE ITEMS AND ADD EVENT LISTENERS
  for (let i = 0; i < preLoad.upgrades.length; i++) {
    item = document.getElementById(preLoad.upgrades[i]);
    item.addEventListener("click", handleBuy);
  }
}
function mine() {
  // HANDLE USER cisco MINING
  preLoad.ciscocoin += preLoad.multipliers[0];
  handleDisplay();
  ciscofall = document.getElementById("mine-div");

  // DISPLAY FALLING cisco COINS
  const ciscofallimg = document.createElement("img");
  ciscofallimg.setAttribute("src", "./img/ciscocoin.png");
  ciscofallimg.classList.add("ciscocoinfall");
  ciscofall.appendChild(ciscofallimg);

  setTimeout(function () {
    ciscofallimg.remove();
  }, 6000);
}

function printNews() {
  // DISPLAY RANDOM NEWS ARTICLE FROM ARRAY
  setTimeout(function () {
    preLoad.header.innerHTML = "";
    let num = Math.floor(Math.random() * preLoad.newsarr.length);
    let text = preLoad.newsarr[num];

    let newsText = document.createElement("div");
    newsText.setAttribute("class", "text");
    newsText.innerText = text;

    preLoad.header.appendChild(newsText);
    printNews();
  }, 2500);
}

function handleBuy() {
  // GET  WORKER AND UPGRADE ARRAYS
  const workerName = preLoad.items;
  const upgradeType = preLoad.upgrades;

  // GET UPGRADE ELEMENTS
  const upgradeImg = document.getElementById(this.id);
  const upgradePrice = document.getElementById(`${this.id}price`);

  // HANDLES MARKETPLACE PURCHASES
  if (preLoad.ciscocoin >= preLoad.itemprices[workerName.indexOf(this.id)]) {
    preLoad.itemquantity[workerName.indexOf(this.id)] += 1;
    preLoad.ciscocoin =
      preLoad.ciscocoin - preLoad.itemprices[workerName.indexOf(this.id)];
    preLoad.totalspent += preLoad.itemprices[workerName.indexOf(this.id)];
    preLoad.itemprices[workerName.indexOf(this.id)] +=
      preLoad.itemquantity[workerName.indexOf(this.id)] *
      preLoad.pricechange[workerName.indexOf(this.id)];

    const worker = document.createElement("img");
    worker.setAttribute("src", `./img/${this.id}.png`);
    worker.style.width = "60px";
    worker.style.height = "auto";
    worker.style.padding = "0";
    worker.classList.add(workerName[workerName.indexOf(this.id)]);
    preLoad.workers.appendChild(worker);
    handleDisplay();
  } else {
  }

  // HANDLES UPGRADE PURCHASES
  if (preLoad.ciscocoin >= preLoad.upgradeprices[upgradeType.indexOf(this.id)]) {
    preLoad.ciscocoin =
      preLoad.ciscocoin - preLoad.upgradeprices[upgradeType.indexOf(this.id)];
    preLoad.totalspent += preLoad.upgradeprices[upgradeType.indexOf(this.id)];

    preLoad.multipliers[upgradeType.indexOf(this.id)] = 2;
    upgradeImg.style.display = "none";
    upgradePrice.style.display = "none";
    handleDisplay();
  }
}
function doMining() {
  // HANDLE WORKER MINING
  setTimeout(function () {
    preLoad.ciscopersecond =
      1 * preLoad.itemquantity[0] * preLoad.multipliers[1] +
      2.5 * preLoad.itemquantity[1] * preLoad.multipliers[2] +
      7.5 * preLoad.itemquantity[2] * preLoad.multipliers[3] +
      15 * preLoad.itemquantity[3] * preLoad.multipliers[4];
    preLoad.ciscocoin += preLoad.ciscopersecond;
    handleDisplay();
    doMining();
  }, 1000);
}

function handleDisplay() {
  //GET STATISTICS TAB
  ciscopersecond = document.getElementById("ciscopersecond");
  totalspent = document.getElementById("totalspent");

  // UPDATE STATISTICS TAB
  preLoad.balance.innerHTML = preLoad.ciscocoin;
  ciscopersecond.innerHTML = `CC per second: ${preLoad.ciscopersecond}`;
  totalspent.innerHTML = `Total CC spent: ${preLoad.totalspent}`;

  // UPDATE MARKETPLACE TAB
  preLoad.items.forEach((name) => {
    namequantity = document.getElementById(`${name}quantity`);
    nameprice = document.getElementById(`${name}price`);

    if (preLoad.ciscocoin >= preLoad.itemprices[preLoad.items.indexOf(name)]) {
      document.getElementById(name).style.opacity = "1";
    } else {
      document.getElementById(name).style.opacity = "0.5";
    }

    namequantity.innerHTML = preLoad.itemquantity[preLoad.items.indexOf(name)];
    nameprice.innerHTML = `${
      preLoad.itemprices[preLoad.items.indexOf(name)]
    } CC`;
  });

  // UPDATE UPGRADES TAB
  preLoad.upgrades.forEach((name) => {
    if (
      preLoad.ciscocoin >= preLoad.upgradeprices[preLoad.upgrades.indexOf(name)]
    ) {
      document.getElementById(name).style.opacity = "1";
    } else {
      document.getElementById(name).style.opacity = "0.5";
    }
  });
}

window.addEventListener("load", preLoad.begin);
window.addEventListener("load", handleDisplay);

printNews();

doMining();
